<!doctype html>
<html lang="en">

<head>
  <title>User Information</title>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Bootstrap CSS v5.2.1 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
</head>

<body>
  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <a class="navbar-brand" href="#"> <div class="logo-image">
            <img src="https://static.vecteezy.com/system/resources/thumbnails/038/516/357/small_2x/ai-generated-eagle-logo-design-in-black-style-on-transparant-background-png.png" alt="BlogApp Logo" width="50" height="50" className="d-inline-block align-top" />
      </div></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="adduser.html">Add User</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  <main>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $db = "management";

   
    $conn = mysqli_connect($servername, $username, $password, $db);

    
    if (!$conn) {
      echo "DB not connected!" . mysqli_connect_error();
      exit();
    }

    // Number of records per page
    $records_per_page = 5;

    // Get the current page from URL parameter, default to 1 if not set
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Ensure current page is within bounds
    $current_page = max(1, $current_page);

    // Calculate the offset for the SQL query
    $offset = ($current_page - 1) * $records_per_page;

    // SQL query to get total number of records
    $sql_count = "SELECT COUNT(*) as total FROM users";
    $result_count = mysqli_query($conn, $sql_count);
    $row_count = mysqli_fetch_assoc($result_count);
    $total_records = $row_count['total'];
    $total_pages = ceil($total_records / $records_per_page);

    // SQL query to fetch user data with pagination
    $sql = "SELECT u.id, u.name, u.email, u.mobile, 
                   COUNT(e.company) AS total_companies, 
                   SUM(e.years) AS total_years,
                   SUM(e.months) AS total_months
            FROM users u
            LEFT JOIN experiences e ON u.id = e.user_id
            GROUP BY u.id
            LIMIT $offset, $records_per_page";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
      echo "<div class='container mt-5'>
              <h2 class='text-center mb-4'>Dashboard</h2>
              <div class='d-flex justify-content-center'>
                <div class='table-responsive'>
                  <table class='table table-bordered table-striped text-center'>
                    <thead class='thead-dark'>
                      <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Total Companies Served</th>
                        <th>Total Experience</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>";
      while ($rows = mysqli_fetch_assoc($result)) {
        $total_months = $rows['total_months'];
        $total_years = $rows['total_years'];
        
        // Convert total months to years and remaining months
        $total_experience_years = $total_years + floor($total_months / 12);
        $remaining_months = $total_months % 12;
        
        echo "<tr>
                <td>{$rows['name']}</td>
                <td>{$rows['email']}</td>
                <td>{$rows['mobile']}</td>
                <td>{$rows['total_companies']}</td>
                <td>{$total_experience_years} years {$remaining_months} months</td>
                <td>
                  <a class='btn btn-warning' href='editform.php?id={$rows['id']}' role='button'>Edit</a>
                  <a class='btn btn-danger' href='delete.php?id={$rows['id']}' role='button'>Delete</a>
                </td>
              </tr>";
      }
      echo "    </tbody>
                </table>
              </div>
            </div>";

      // Pagination controls
      echo "<nav aria-label='Page navigation'>
              <ul class='pagination justify-content-center'>";
      for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $current_page) ? ' active' : '';
        echo "<li class='page-item$active'><a class='page-link' href='?page=$i'>$i</a></li>";
      }
      echo "    </ul>
            </nav>";

    } else {
      echo "<div class='container mt-4'><div class='alert alert-warning' role='alert'>No Record Found!</div></div>";
    }

    // Close connection
    mysqli_close($conn);
    ?>
  </main>
  <div>
        <center>
          <footer class="footer mt-auto py-3 fixed-bottom">
            <div class="container">
              <span class="text-muted">
                &copy; CrayonInfoTech_Task. All rights reserved 2024.
              </span>
            </div>
          </footer>
        </center>
      </div>
  <!-- Bootstrap JavaScript Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
  </script>
</body>

</html>
