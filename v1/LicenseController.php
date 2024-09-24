<?php

require_once 'functions.php';
require_once 'License.php';

/**
 * Summary of LicenseController
 */
class LicenseController
{
    public function __construct()
    {
        $this->conn = connectDB();
    }

    public function getAllProjects()
    {
        $sql = "SELECT * FROM projects";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $projects = [];
            while ($row = $result->fetch_assoc()) {
                $projects[] = new License($row);
            }
            return $projects;
        } else {
            return [];
        }
    }

    public function createProject($data)
    {
        $license = new License($data);
        $query = "INSERT INTO projects (user_name, project_name, project_url, license_key, start_date, end_date, email, license_type, redirect_url, is_lifetime, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $licenseData = $license->toArray();
        $stmt->bind_param('sssssssssiiss', ...array_values($licenseData));
        $result = $stmt->execute();

        return $result;
    }

    public function updateProject($id, $data)
    {
        $license = new License($data);
        $query = "UPDATE projects SET user_name=?, project_name=?, project_url=?, license_key=?, start_date=?, end_date=?, email=?, license_type=?, redirect_url=?, is_lifetime=?, updated_at=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $licenseData = array_merge($license->toUpdateArray(), ['id' => $id]);
        $stmt->bind_param('sssssssssiis', ...array_values($licenseData));
        $result = $stmt->execute();

        return $result;
    }

    public function toggleProjectStatus($id)
    {
        $query = "UPDATE projects SET status = !status WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();

        return $result;
    }

    public function filterProjectsByName($name)
    {
        $query = "SELECT * FROM projects WHERE project_name LIKE ?";
        $stmt = $this->conn->prepare($query);
        $searchName = '%' . $name . '%';
        $stmt->bind_param('s', $searchName);
        $stmt->execute();
        $result = $stmt->get_result();

        $licenseList = [];
        while ($row = $result->fetch_assoc()) {
            $licenseList[] = new License($row);
        }

        return $licenseList;
    }

    public function getLicenseByUrl($projectUrl)
    {
        $sql = "SELECT * FROM projects WHERE project_url = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $projectUrl);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_object();
    }

    public function getProjectById($id)
    {
        $sql = "SELECT * FROM projects WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_object();
    }
    
    public function deleteProject($id)
    {
        $query = "DELETE FROM projects WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();

        return $result;
    }

    public function getLicenseByKey($licenseKey)
    {
        $sql = "SELECT * FROM projects WHERE license_key = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $licenseKey);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_object();
    }

    public function isLicenseValid($license)
    {
        // Verificar si la licencia está activa
        if (!$license->status) {
            return false;
        }

        // Verificar si la licencia es vitalicia
        if ($license->is_lifetime) {
            return true;
        }

        // Verificar si la licencia no ha expirado
        $currentDate = new DateTime();
        $endDate = new DateTime($license->end_date);

        return $currentDate <= $endDate;
    }

    /**
     * Summary of getLicenseByUrl
     * @param mixed $projectUrl
     * @return mixed
     */


}
