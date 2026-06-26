<?php
/**
 * YTech Panels - Admin Profile Page
 */
include './header.php';

$db = getDB();
$adminId = (int) $_SESSION['admin_id'];

$stmt = $db->prepare("SELECT id, name, email, mobile, username, profile_pic, last_login, created_at FROM admin_users WHERE id = :id");
$stmt->execute([':id' => $adminId]);
$admin = $stmt->fetch();

$profilePicPath = !empty($admin['profile_pic'])
    ? './src/images/profile_picture/' . htmlspecialchars($admin['profile_pic'])
    : './src/images/user-avtar.png';
?>

<style>
    .profile-card { background: #fff; border: 1px solid #e2e8f0; padding: 0; margin-bottom: 24px; }
    .profile-card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 16px 24px; font-weight: 600; font-size: 15px; color: #1e293b; display: flex; align-items: center; gap: 10px; }
    .profile-card-header i { color: #003a8c; }
    .profile-card-body { padding: 24px; }
    .profile-avatar-section { text-align: center; padding: 30px 24px; background: #f8fafc; border: 1px solid #e2e8f0; margin-bottom: 24px; }
    .profile-avatar-wrapper { position: relative; display: inline-block; margin-bottom: 16px; }
    .profile-avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #e2e8f0; }
    .avatar-overlay { position: absolute; bottom: 4px; right: 4px; background: #003a8c; color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid #fff; font-size: 13px; transition: background 0.2s; }
    .avatar-overlay:hover { background: #002a6c; }
    .profile-name-display { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .profile-email-display { font-size: 13px; color: #64748b; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
    .info-item { margin-bottom: 4px; }
    .info-item label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block; }
    .info-item span { font-size: 14px; color: #1e293b; font-weight: 500; }
    .profile-card .form-group label { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 6px; display: block; }
    .profile-card .form-control { border: 1.5px solid #e2e8f0; border-radius: 4px; padding: 10px 14px; font-size: 14px; transition: border-color 0.2s; width: 100%; }
    .profile-card .form-control:focus { border-color: #003a8c; box-shadow: 0 0 0 3px rgba(0, 58, 140, 0.08); outline: none; }
    .btn-primary-custom { background: #003a8c; color: #fff; border: none; padding: 10px 28px; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s; }
    .btn-primary-custom:hover { background: #002a6c; }
    .btn-danger-custom { background: #dc2626; color: #fff; border: none; padding: 10px 28px; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s; }
    .btn-danger-custom:hover { background: #b91c1c; }
    .password-strength { height: 4px; background: #e2e8f0; margin-top: 8px; border-radius: 2px; overflow: hidden; }
    .password-strength-bar { height: 100%; width: 0%; transition: width 0.3s, background 0.3s; border-radius: 2px; }
    .password-requirements { margin-top: 8px; font-size: 12px; color: #64748b; padding-left: 0; }
    .password-requirements li { margin-bottom: 2px; list-style: none; }
    .password-requirements li i { margin-right: 6px; font-size: 11px; }
    .password-requirements li.met { color: #16a34a; }
    .password-requirements li.unmet { color: #dc2626; }
    @media (max-width: 768px) { .info-grid { grid-template-columns: 1fr; } }
</style>

<div class="row">
    <div class="col-lg-4">
        <div class="profile-avatar-section">
            <div class="profile-avatar-wrapper">
                <img src="<?= $profilePicPath ?>" alt="Profile" class="profile-avatar" id="avatarPreview">
                <label class="avatar-overlay" for="avatarInput" title="Change profile picture"><i class="fas fa-camera"></i></label>
            </div>
            <div class="profile-name-display"><?= htmlspecialchars($admin['name']) ?></div>
            <div class="profile-email-display"><?= htmlspecialchars($admin['email']) ?></div>
        </div>
        <div class="profile-card">
            <div class="profile-card-header"><i class="fas fa-info-circle"></i> Account Information</div>
            <div class="profile-card-body">
                <div class="info-grid">
                    <div class="info-item"><label>Username</label><span><?= htmlspecialchars($admin['username']) ?></span></div>
                    <div class="info-item"><label>Mobile</label><span><?= htmlspecialchars($admin['mobile'] ?? '---') ?></span></div>
                    <div class="info-item"><label>Last Login</label><span><?= $admin['last_login'] ? date('d M Y, h:i A', strtotime($admin['last_login'])) : '---' ?></span></div>
                    <div class="info-item"><label>Member Since</label><span><?= $admin['created_at'] ? date('d M Y', strtotime($admin['created_at'])) : '---' ?></span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="profile-card">
            <div class="profile-card-header"><i class="fas fa-user-edit"></i> Edit Profile</div>
            <div class="profile-card-body">
                <form id="profileForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="file" name="profile_pic" id="avatarInput" accept="image/*" style="display:none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group"><label for="name">Full Name *</label><input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required maxlength="255"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label for="email">Email Address *</label><input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label for="mobile">Mobile Number</label><input type="text" class="form-control" id="mobile" name="mobile" value="<?= htmlspecialchars($admin['mobile'] ?? '') ?>" placeholder="+91-XXXXXXXXXX"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label for="username">Username *</label><input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+"><small style="color:#64748b;">Letters, numbers, underscores only.</small></div>
                        </div>
                    </div>
                    <div class="mt-3"><button type="submit" class="btn-primary-custom" id="profileSubmitBtn"><i class="fas fa-save"></i> Save Changes</button></div>
                </form>
            </div>
        </div>
        <div class="profile-card">
            <div class="profile-card-header"><i class="fas fa-lock"></i> Change Password</div>
            <div class="profile-card-body">
                <form id="passwordForm">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group"><label for="current_password">Current Password *</label><input type="password" class="form-control" id="current_password" name="current_password" requir
