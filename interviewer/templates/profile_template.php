<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Profile</h1>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php 
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                value="<?php echo htmlspecialchars($interviewer['first_name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                value="<?php echo htmlspecialchars($interviewer['last_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                            value="<?php echo htmlspecialchars($interviewer['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                            value="<?php echo htmlspecialchars($interviewer['phone'] ?? ''); ?>">
                    </div>
                    <hr>
                    <h6 class="m-0 font-weight-bold text-primary mb-3">Change Password</h6>
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Program Information -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Program Assignment</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="font-weight-bold">College:</label>
                    <p><?php echo htmlspecialchars($interviewer['college_name'] ?? 'Not assigned'); ?></p>
                </div>
                <div class="mb-3">
                    <label class="font-weight-bold">Program:</label>
                    <p><?php echo htmlspecialchars($interviewer['program_name'] ?? 'Not assigned'); ?></p>
                </div>
                <div class="mb-3">
                    <label class="font-weight-bold">Role:</label>
                    <p>Program Head / Interviewer</p>
                </div>
            </div>
        </div>
    </div>
</div> 