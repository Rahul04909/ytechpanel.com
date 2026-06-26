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
                        <div class="col-md-6"><div class="form-group"><label for="name">Full Name *</label><input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required maxlength="255"></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="email">Email Address *</label><input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="mobile">Mobile Number</label><input type="text" class="form-control" id="mobile" name="mobile" value="<?= htmlspecialchars($admin['mobile'] ?? '') ?>" placeholder="+91-XXXXXXXXXX"></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="username">Username *</label><input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+"><small style="color:#646970;">Letters, numbers, underscores only.</small></div></div>
                    </div>
                    <div class="mt-3"><button type="submit" class="wp-btn-primary" id="profileSubmitBtn"><i class="fas fa-save"></i> Save Changes</button></div>
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
                        <div class="col-md-12"><div class="form-group"><label for="current_password">Current Password *</label><input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password"></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="new_password">New Password *</label><input type="password" class="form-control" id="new_password" name="new_password" required autocomplete="new-password"><div class="wp-password-strength"><div class="wp-password-strength-bar" id="strengthBar"></div></div><ul class="wp-password-reqs" id="passwordRequirements"><li id="req-length" class="unmet"><i class="fas fa-times-circle"></i> At least 8 characters</li><li id="req-upper" class="unmet"><i class="fas fa-times-circle"></i> One uppercase letter</li><li id="req-lower" class="unmet"><i class="fas fa-times-circle"></i> One lowercase letter</li><li id="req-number" class="unmet"><i class="fas fa-times-circle"></i> One number</li><li id="req-special" class="unmet"><i class="fas fa-times-circle"></i> One special character</li></ul></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="confirm_password">Confirm New Password *</label><input type="password" class="form-control" id="confirm_password" name="confirm_password" required autocomplete="new-password"><small id="matchHint" style="display:none;margin-top:4px;"></small></div></div>
                    </div>
                    <div class="mt-3"><button type="submit" class="wp-btn-danger" id="passwordSubmitBtn"><i class="fas fa-key"></i> Update Password</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-card{background:#fff;border:1px solid #e2e8f0;margin-bottom:24px}
    .profile-card-header{background:#f1f1f1;border-bottom:1px solid #e2e8f0;padding:16px 24px;font-weight:600;font-size:15px;color:#1d2327;display:flex;align-items:center;gap:10px}
    .profile-card-header i{color:#2271b1}
    .profile-card-body{padding:24px}
    .profile-avatar-section{text-align:center;padding:30px 24px;background:#fff;border:1px solid #e2e8f0;margin-bottom:24px}
    .profile-avatar-wrapper{position:relative;display:inline-block;margin-bottom:16px}
    .profile-avatar{width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #e2e8f0}
    .avatar-overlay{position:absolute;bottom:4px;right:4px;background:#2271b1;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid #fff;font-size:13px;transition:background .2s}
    .avatar-overlay:hover{background:#135e96}
    .profile-name-display{font-size:20px;font-weight:700;color:#1d2327;margin-bottom:4px}
    .profile-email-display{font-size:13px;color:#646970}
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px 24px}
    .info-item{margin-bottom:4px}
    .info-item label{font-size:11px;font-weight:600;color:#646970;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;display:block}
    .info-item span{font-size:14px;color:#1d2327;font-weight:500}
    .profile-card .form-group label{font-size:13px;font-weight:600;color:#1d2327;margin-bottom:6px;display:block}
    .profile-card .form-control{border:1px solid #8c8f94;border-radius:4px;padding:10px 14px;font-size:14px;transition:border-color .2s;width:100%}
    .profile-card .form-control:focus{border-color:#2271b1;box-shadow:0 0 0 1px #2271b1;outline:none}
    .wp-btn-primary{background:#2271b1;color:#fff;border:1px solid #2271b1;padding:8px 16px;font-weight:600;font-size:13px;cursor:pointer;border-radius:3px;transition:background .2s}
    .wp-btn-primary:hover{background:#135e96;border-color:#135e96}
    .wp-btn-danger{background:#b32d2e;color:#fff;border:1px solid #b32d2e;padding:8px 16px;font-weight:600;font-size:13px;cursor:pointer;border-radius:3px;transition:background .2s}
    .wp-btn-danger:hover{background:#a00;border-color:#a00}
    .wp-password-strength{height:4px;background:#e2e8f0;margin-top:8px;border-radius:2px;overflow:hidden}
    .wp-password-strength-bar{height:100%;width:0;transition:width .3s,background .3s;border-radius:2px}
    .wp-password-reqs{margin-top:8px;font-size:12px;color:#646970;padding-left:0;list-style:none}
    .wp-password-reqs li{margin-bottom:2px}
    .wp-password-reqs li i{margin-right:6px;font-size:11px}
    .wp-password-reqs li.met{color:#00a32a}
    .wp-password-reqs li.unmet{color:#b32d2e}
    @media(max-width:768px){.info-grid{grid-template-columns:1fr}}
</style>

<script>
$(document).ready(function(){
    $("#avatarInput").on("change",function(e){
        var file=e.target.files[0];
        if(file){var r=new FileReader();r.onload=function(ev){$("#avatarPreview").attr("src",ev.target.result)};r.readAsDataURL(file)}
    });
    $("#profileForm").on("submit",function(e){
        e.preventDefault();var btn=$("#profileSubmitBtn");btn.prop("disabled",true).html("<i class=\"fas fa-spinner fa-spin\"></i> Saving...");
        $.ajax({url:"handlers/profile-handler.php",type:"POST",data:new FormData(this),processData:false,contentType:false,dataType:"json",
            success:function(res){if(res.success){Swal.fire({icon:"success",title:"Updated!",text:res.message,timer:2000,showConfirmButton:false}).then(function(){if(res.profile_pic){$(".user-panel img").attr("src","./src/images/profile_picture/"+res.profile_pic)}location.reload()})}else{Swal.fire({icon:"error",title:"Error",text:res.message})}},
            error:function(){Swal.fire({icon:"error",title:"Error",text:"Network error."})},
            complete:function(){btn.prop("disabled",false).html("<i class=\"fas fa-save\"></i> Save Changes")}
        });
    });
    $("#new_password").on("input",function(){
        var pwd=$(this).val(),score=0;
        var checks=[{el:"req-length",test:pwd.length>=8},{el:"req-upper",test:/[A-Z]/.test(pwd)},{el:"req-lower",test:/[a-z]/.test(pwd)},{el:"req-number",test:/\\d/.test(pwd)},{el:"req-special",test:/[^A-Za-z0-9]/.test(pwd)}];
        checks.forEach(function(c){if(c.test){$("#"+c.el).removeClass("unmet").addClass("met").find("i").removeClass("fa-times-circle").addClass("fa-check-circle");score++}else{$("#"+c.el).removeClass("met").addClass("unmet").find("i").removeClass("fa-check-circle").addClass("fa-times-circle")}});
        var pct=(score/5)*100,colors=["#b32d2e","#b32d2e","#dba617","#dba617","#00a32a","#00a32a"];
        $("#strengthBar").css({width:pct+"%",background:colors[score]});
    });
    $("#confirm_password").on("input",function(){
        var np=$("#new_password").val(),c=$(this).val(),h=$("#matchHint");
        if(c.length>0){h.show();if(np===c){h.html("<i class=\"fas fa-check-circle\" style=\"color:#00a32a\"></i> Passwords match").css("color","#00a32a")}else{h.html("<i class=\"fas fa-times-circle\" style=\"color:#b32d2e\"></i> Passwords do not match").css("color","#b32d2e")}}else{h.hide()}
    });
    $("#passwordForm").on("submit",function(e){
        e.preventDefault();var btn=$("#passwordSubmitBtn"),np=$("#new_password").val(),cp=$("#confirm_password").val();
        if(np!==cp){Swal.fire({icon:"error",title:"Mismatch",text:"Passwords do not match."});return}
        if(np.length<8){Swal.fire({icon:"error",title:"Too Short",text:"Password must be at least 8 characters."});return}
        btn.prop("disabled",true).html("<i class=\"fas fa-spinner fa-spin\"></i> Updating...");
        $.ajax({url:"handlers/profile-handler.php",type:"POST",data:$(this).serialize(),dataType:"json",
            success:function(res){if(res.success){Swal.fire({icon:"success",title:"Password Updated!",text:res.message,timer:2000,showConfirmButton:false}).then(function(){$("#passwordForm")[0].reset();$("#strengthBar").css({width:"0%"});$(".wp-password-reqs li").removeClass("met").addClass("unmet").find("i").removeClass("fa-check-circle").addClass("fa-times-circle");$("#matchHint").hide()})}else{Swal.fire({icon:"error",title:"Error",text:res.message})}},
            error:function(){Swal.fire({icon:"error",title:"Error",text:"Network error."})},
            complete:function(){btn.prop("disabled",false).html("<i class=\"fas fa-key\"></i> Update Password")}
        });
    });
});
</script>

<?php include './footer.php'; ?>
