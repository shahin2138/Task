<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $conn = new mysqli('localhost', 'root', '', 'management');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // user data
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // user experiences
    $sql = "SELECT * FROM experiences WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $experiences = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit User</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <header>
        <!-- place navbar here -->
    </header>
    <main>
        <div class="container border border-5 border-dark my-5 p-5">
            <div class="container mt-5">
                <h2 class="text-center mb-4">Edit User Information</h2>
                <form id="UserForm" method="post" action="UserUpdate.php">
                    <input type="hidden" class="form-control" id="id" name="id" value="<?php echo $user['id']; ?>" required>

                    <div class="form-group mb-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                        <small id="nameHelp" class="form-text text-muted"></small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="mobile">Mobile</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo $user['mobile']; ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                            <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>

                    <h3 class="mt-4 mb-3">Experiences</h3>
                    <div id="experienceFields">
                        <?php foreach ($experiences as $experience) : ?>
                            <div class="experience-group border border-light p-3 mb-3">
                                <input type="hidden" name="experience_id[]" value="<?php echo $experience['id']; ?>">
                                <div class="form-group mb-2">
                                    <label for="company">Company</label>
                                    <input type="text" class="form-control" name="company[]" value="<?php echo $experience['company']; ?>" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="years">Years</label>
                                    <input type="number" class="form-control" name="years[]" value="<?php echo $experience['years']; ?>" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label for="months">Months</label>
                                    <input type="number" class="form-control" name="months[]" value="<?php echo $experience['months']; ?>" required>
                                </div>
                                <button type="button" class="btn btn-danger remove-experience">Remove</button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="btn btn-success mt-3" id="addExperience">Add Company</button>
                    <button type="submit" class="btn btn-primary mt-3">Update</button>
                </form>
            </div>
        </div>
    </main>
    <div>
        <center>
          <footer class="footer mt-auto py-3 sticky-bottom">
            <div class="container">
              <span class="text-muted">
                &copy; CrayonInfoTech_Task. All rights reserved 2024.
              </span>
            </div>
          </footer>
        </center>
      </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.remove-experience').forEach(function(button) {
                button.addEventListener('click', function() {
                    this.closest('.experience-group').remove();
                });
            });

            document.getElementById('addExperience').addEventListener('click', function() {
                const experienceGroup = document.createElement('div');
                experienceGroup.classList.add('experience-group', 'border', 'border-light', 'p-3', 'mb-3');

                experienceGroup.innerHTML = `
                    <div class="form-group mb-2">
                        <label for="company">Company</label>
                        <input type="text" class="form-control" name="company[]" required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="years">Years</label>
                        <input type="number" class="form-control" name="years[]" required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="months">Months</label>
                        <input type="number" class="form-control" name="months[]" required>
                    </div>
                    <button type="button" class="btn btn-danger remove-experience">Remove</button>
                `;

                document.getElementById('experienceFields').appendChild(experienceGroup);

                experienceGroup.querySelector('.remove-experience').addEventListener('click', function() {
                    experienceGroup.remove();
                });
            });

            document.getElementById('name').addEventListener('blur', function() {
                var name = this.value;
                if (!/^[a-zA-Z]+$/.test(name)) {
                    document.getElementById('nameHelp').textContent = 'Name should only contain alphabets';
                    document.getElementById('nameHelp').style.color = 'red';
                } else {
                    document.getElementById('nameHelp').textContent = '';
                }
            });

            document.getElementById('UserForm').addEventListener('submit', function(e) {
                var name = document.getElementById('name').value;
                var nameHelpText = document.getElementById('nameHelp').textContent;
                if (!/^[a-zA-Z]+$/.test(name) || nameHelpText === 'Name should only contain alphabets') {
                    e.preventDefault();
                    alert('Please provide a valid name. Name should only contain alphabets.');
                }
            });
        });
    </script>
</body>
</html>
