<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection file
include 'connection.php';

// Fetch user data from login table
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, image FROM login WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$username = "ADMIN 123";
$imageData = null;

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $row['username'];
    $imageData = $row['image'];
}

// Get filter value (default to all if not set)
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Search functionality
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination logic
$limit = 8; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page
$start = ($page - 1) * $limit; // Calculate the starting row

// Prepare the base query with a WHERE clause for search term
$sql = "
    SELECT 
        r.ticket_number,
        r.violation_date,
        r.first_name,
        r.last_name,
        COALESCE(v.STATUS, v2.STATUS, v3.STATUS) AS payment_status,
        CASE 
            WHEN v.ticket_number IS NOT NULL THEN 'First Violation'
            WHEN v2.ticket_number IS NOT NULL THEN 'Second Violation'
            WHEN v3.ticket_number IS NOT NULL THEN 'Third Violation'
            ELSE 'Unknown'
        END AS violation_level,
        CONCAT(
            IFNULL(v.first_violation, ''),
            IFNULL(v.others_violation, ''),
            IFNULL(v2.second_violation, ''),
            IFNULL(v2.others_violation, ''),
            IFNULL(v3.third_violation, ''),
            IFNULL(v3.others_violation, '')
        ) AS violations
    FROM 
        report AS r
    LEFT JOIN 
        violation AS v ON r.ticket_number = v.ticket_number
    LEFT JOIN 
        2_violation AS v2 ON r.ticket_number = v2.ticket_number
    LEFT JOIN 
        3_violation AS v3 ON r.ticket_number = v3.ticket_number
    WHERE 
        (r.ticket_number LIKE :searchTerm
        OR r.first_name LIKE :searchTerm
        OR r.last_name LIKE :searchTerm)
";

// Add a filter condition if a specific violation level is selected
if ($filter) {
    $sql .= " AND 
        CASE 
            WHEN v.ticket_number IS NOT NULL THEN 'First Violation'
            WHEN v2.ticket_number IS NOT NULL THEN 'Second Violation'
            WHEN v3.ticket_number IS NOT NULL THEN 'Third Violation'
        END = :filter";
}

// Add pagination limit
$sql .= " LIMIT :start, :limit";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':searchTerm', '%' . $searchTerm . '%');  // Wildcards for partial match
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

// Bind filter parameter if a filter is applied
if ($filter) {
    $stmt->bindValue(':filter', $filter);
}

$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of records to calculate total pages
$totalStmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM report AS r
    LEFT JOIN 
        violation AS v ON r.ticket_number = v.ticket_number
    LEFT JOIN 
        2_violation AS v2 ON r.ticket_number = v2.ticket_number
    LEFT JOIN 
        3_violation AS v3 ON r.ticket_number = v3.ticket_number
    WHERE 
        (r.ticket_number LIKE :searchTerm
        OR r.first_name LIKE :searchTerm
        OR r.last_name LIKE :searchTerm)
");

// Add the filter to the total count query
if ($filter) {
    $totalStmt->bindValue(':filter', $filter);
}
$totalStmt->bindValue(':searchTerm', '%' . $searchTerm . '%');
$totalStmt->execute();
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="/POSO/images/poso.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="/POSO/admin/css/d_style.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Sidebar and main layout styling (existing) */
        /* Pagination container */
        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        /* Pagination buttons */
        .pagination-btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #007bff;  /* Blue background */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.2s;
        }

        /* Add hover effect */
        .pagination-btn:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05); /* Slight zoom effect */
        }

        /* Styling for the Previous and Next buttons */
        .pagination-btn.previous {
            background-color: #28a745;  /* Green background for Previous */
        }

        .pagination-btn.next {
            background-color: #dc3545;  /* Red background for Next */
        }

        /* Active state when hovering over or clicking */
        .pagination-btn:active {
            transform: scale(1); /* Remove zoom effect on click */
        }

        /* Styling for the filter dropdown and buttons */
        .search-filter form {
            display: inline-block;
            margin-right: 20px;
        }

        .search-filter select,
        .search-filter input[type="text"] {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 2px solid #007bff;  /* Blue border */
            margin-right: 10px;
            outline: none;
            width: 250px;
        }

        .search-filter select:focus,
        .search-filter input[type="text"]:focus {
            border-color: #0056b3;  /* Darker blue on focus */
        }

        .search-filter button {
            background-color: #007bff;  /* Blue background */
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        /* Add hover effect */
        .search-filter button:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05); /* Slight zoom effect */
        }

        /* Styling for the filter button */
        .search-filter button i {
            margin-right: 8px;
        }

        .search-filter button:active {
            transform: scale(1); /* Remove zoom effect on click */
        }

        /* Add media query for mobile responsiveness */
        @media (max-width: 768px) {
            .pagination-btn {
                padding: 8px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="/POSO/images/right.png" alt="POSO Logo">
        </div>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="report.php" class="active"><i class="fas fa-file-alt"></i> Reports</a></li>
            <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <header>
            <img src="/POSO/images/left.png" alt="City Logo">
            <h1>PUBLIC ORDER & SAFETY OFFICE<br>CITY OF BIÑAN</h1>
            <img src="/POSO/images/arman.png" alt="POSO Logo">
        </header>
        
        
        <div class="search-filter">
            <form action="report.php" method="get">
                <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <select name="filter">
                    <option value="">All</option>
                    <option value="First Violation" <?php echo ($filter == 'First Violation') ? 'selected' : ''; ?>>First Violation</option>
                    <option value="Second Violation" <?php echo ($filter == 'Second Violation') ? 'selected' : ''; ?>>Second Violation</option>
                    <option value="Third Violation" <?php echo ($filter == 'Third Violation') ? 'selected' : ''; ?>>Third Violation</option>
                </select>
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>

        <table border="1" style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Ticket No.</th>
                    <th>Name</th>
                    <th>Violation Level</th>
                    <th>Violation/s</th> <!-- New column for Violations -->
                    <th>Violation Date</th>
                    <th>Status</th>
                    <th>Action</th> <!-- New column for the View action -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['ticket_number']); ?></td>
                        <td><?php echo htmlspecialchars($report['first_name']) . ' ' . htmlspecialchars($report['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($report['violation_level']); ?></td>
                        <td><?php echo htmlspecialchars($report['violations']); ?></td> <!-- Display combined violations -->
                        <td><?php echo htmlspecialchars($report['violation_date']); ?></td>
                        <td><?php echo htmlspecialchars($report['payment_status']); ?></td>
                        <td>
                            <a href="sm.php?ticket_number=<?php echo htmlspecialchars($report['ticket_number']); ?>" class="pagination-btn">View</a>
                        </td> <!-- View link -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
    <?php 
        // Show previous button only if not on the first page
        if ($page > 1): 
    ?>
        <a href="?page=<?php echo max(1, $page - 1); ?>&search=<?php echo urlencode($searchTerm); ?>&filter=<?php echo urlencode($filter); ?>" class="pagination-btn previous">Prev</a>
    <?php endif; ?>

    <?php 
        // Display numbered pagination links
        for ($i = 1; $i <= $totalPages; $i++):
            $activeClass = ($i == $page) ? 'active' : '';  // Highlight the current page
    ?>
        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchTerm); ?>&filter=<?php echo urlencode($filter); ?>" class="pagination-btn <?php echo $activeClass; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php 
        // Show next button only if not on the last page
        if ($page < $totalPages): 
    ?>
        <a href="?page=<?php echo min($totalPages, $page + 1); ?>&search=<?php echo urlencode($searchTerm); ?>&filter=<?php echo urlencode($filter); ?>" class="pagination-btn next">Next</a>
    <?php endif; ?>
</div>


    </div>
</body>
</html>
