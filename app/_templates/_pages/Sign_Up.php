
<main class="bottom_wrapper">
<h1 class="pg_hd">Create your LiiVid Account</h1>
<div id="pagination-top" style="padding: 25px 0pt 5px; text-align: right;"></div>
<aside class="re_l">
  <aside class="re_l">
    <div id="signup-text">
      <div style="font-weight:bold;" class="signup-title">Join the best VidLii style video-sharing community!</div>
      <p>Sign up now to get full access with your LiiVid account:</p>
      <ul>
        <li>Comment, rate, and make video responses to your favorite videos</li>
        <li>Upload and share your videos with millions of other users</li>
        <li>Save your favorite videos to watch and share later</li>
        <li>Enter your videos into contests for fame and prizes</li>
      </ul>
    </div>
    <br>

    <div style="font-weight:bold">Already have an account?</div><br>
    <div class="you_wnt" id="login_box">
      <div>
        <form action="/login" method="POST">
          <img src="/img/liivid.png" height="37" width="92" alt="LiiVid" title="LiiVid - Showcase Yourself.">
          <table cellpadding="3">
            <tr>
              <input type="hidden" name="jf1" value="acnla">
              <td align="right"><label for="lg_username">Username:</label></td>
              <td align="left"><input type="text" name="v_username" id="lg_username" required autofocus></td>
            </tr>
            <tr>
              <td align="right"><label for="password">Password:</label></td>
              <td align="left"><input id="password" name="password" type="password" required></td>
            </tr>
            <tr>
              <td></td>
              <td align="left">
                <div style="margin-bottom:8px">
                  <input type="checkbox" id="remember" name="remember" checked>
                  <label for="remember">Remember Me</label>
                </div>
                <input type="submit" class="search_button" style="padding:2.5px 10px" name="submit_login" value="Sign In">
              </td>
            </tr>
          </table>
          <div class="log_fgt">
            <a href="javascript:void(0)" onclick="alert('You can also log in with your email instead of your username!')">Forgot Username</a> |
            <a href="/forgot_password">Forgot Password</a>
          </div>
        </form>
      </div>
    </div>
  </aside>
</aside>

<section class="re_r"><div class="signup-title" style="padding-bottom: 4px;font-weight:bold">Create Your LiiVid Account</div>
  <div class="you_wnt" id="re_box">
    <script src="/js/pw_check2.js"></script>
    <div>
      <form action="" method="POST" name="userform" onsubmit="return submit_button(document.forms.userform.password.value)">

        <?php if ($_PAGE->has_errors()) : ?>
          <div style="color:red;margin:0 0 6px;text-align:center">
            <?= $_PAGE->return_errors()[0] ?>
          </div>
        <?php endif ?>

        <table style="margin:0 auto;" cellpadding="6">
          <tr>
            <td align="right"><label for="re_email">Email:</label></td>
            <td>
              <input type="email" name="email" id="re_email" maxlength="128" size="40"
                value="<?= isset($Validation['email']) ? htmlspecialchars($Validation['email']) : '' ?>"
                spellcheck="false" autofocus required>
            </td>
          </tr>

          <tr>
            <td align="right" valign="top"><label for="re_username" style="position:relative;top:3px">Username:</label></td>
            <td>
              <input type="text" maxlength="20" name="vl_usernames" id="re_username" size="40"
                value="<?= isset($Validation['vl_username']) ? htmlspecialchars($Validation['vl_username']) : '' ?>"
                autocomplete="off" required pattern="[a-zA-Z0-9 ]+" spellcheck="false"
                onblur="if(this.value !== '') { user_exists(this.value); }">
              <div id="user_exists" style="display:none;color:red;font-size:12px;position:relative;top:1px;left:1px"></div>
            </td>
          </tr>

          <tr>
            <td align="right" valign="top"><label for="pw1" style="position:relative; top:3px">Password:</label></td>
            <td>
              <input type="password" maxlength="128" name="password" id="pw1" size="40"
                onkeyup="if (this.value.length > 0) { check_password(document.forms.userform.password.value) }" required><br>
              <div id="pwd_bar" style="position:relative;top:7px"></div>
              <span id="pwd_meter" style="position:relative;top:7px;font-size:12.5px"></span>
            </td>
          </tr>

          <tr>
            <td align="right"><label for="pw2">Repeat Password:</label></td>
            <td><input type="password" maxlength="128" name="password2" id="pw2" size="40" required></td>
          </tr>

          <tr>
            <td align="right"><label>Country:</label></td>
            <td>
              <select name="country" style="width:310px;">
                <?php foreach ($Countries as $Country => $Name) : ?>
                  <option value="<?= $Country ?>" <?= (isset($_POST['country']) && $_POST['country'] == $Country) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($Name) ?>
                  </option>
                <?php endforeach ?>
              </select>
            </td>
          </tr>

          <tr>
            <td align="right"><label>Birthday:</label></td>
            <td>
              <select name="month">
                <?php foreach ($Months_Array as $label => $value) : ?>
                  <option value="<?= $value ?>" <?= (!empty($_POST) && $_POST['month'] == $value) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($label) ?>
                  </option>
                <?php endforeach ?>
              </select>

              <select name="day">
                <?php for ($d = 1; $d <= 31; $d++) : ?>
                  <option value="<?= $d ?>" <?= (!empty($_POST) && $_POST['day'] == $d) ? 'selected' : '' ?>><?= $d ?></option>
                <?php endfor ?>
              </select>

              <select name="year">
                <?php for ($y = date('Y'); $y >= 1910; $y--) : ?>
                  <option value="<?= $y ?>" <?= (!empty($_POST) && $_POST['year'] == $y) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor ?>
              </select>
            </td>
          </tr>

          <tr>
            <td></td>
            <td align="center">
              <?= captcha(5, 10, 8, true) ?>
            </td>
          </tr>

          <tr>
            <td>Terms of Use,<br>Privacy Policy:</td>
            <td>
              <label style="position:relative;right:2.5px;">
                <input type="checkbox" id="termsCheckbox" name="age" <?= isset($_POST['age']) ? 'checked' : '' ?> required>
                I agree to the <a href="/terms" target="_blank">Terms of Use</a> and
                <a href="/privacy" target="_blank">Privacy Policy</a>.
              </label>
            </td>
          </tr>

          <tr>
            <td></td>
            <td id="termsWarning" align="center" style="display:none;background:#ffaeae">
              Sorry, you need to agree to the Terms of Use<br>and Privacy Policy to create an account
            </td>
          </tr>
        </table>

       <div style="text-align: center;margin:8px 0 0 0">
    <input name="submit_register" class="search_button"
    onclick="if(!document.getElementById('termsCheckbox').checked){document.getElementById('termsWarning').style.display='block';return false;}if(document.getElementById('pw1').value !== document.getElementById('pw2').value){ alert('The passwords do not match!'); document.getElementById('pw2').value = ''; document.getElementById('pw1').value = ''; $('#pw1').focus(); check_password(document.forms.userform.password.value); return false; }"
    id="reg_submit" type="submit" value="Create my account">
</div>

      </form>
    </div>
  </div>
</section>