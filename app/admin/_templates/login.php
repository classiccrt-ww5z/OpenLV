
<div style="float:left;text-align: center;padding: 20px 0">
    
    <div class="you_wnt" id="login_box">
        <div >
            <form action="/admin/login" method="POST">
                        <table cellpadding="3">
                <tbody><tr>
                    <input type="hidden" name="jf1" value="acnla">
                    <td align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td align="left">Username:<br><input size="40" type="text" name="user_name" id="lg_username" required="" autofocus=""></td>
                </tr>
                <tr>
                    <td align="right">&nbsp;</td>
                    <td align="left">Password:<br><input size="40" id="password" name="user_password" type="password" required=""></td>
                </tr>
                 <tr>
                    <td align="right">&nbsp;</td>
                    <td align="left">
                        Panel Password:<br>
                        <input size="40" id="password" name="panel_password" type="password" required=""></td>
                </tr>
          <tr>
                        <td></td>
                        <td align="left">
                           
                           
                        </td>
                    </tr>
            </tbody></table>
<div style="display: flex; justify-content: center;">
    <?= captcha(0) ?>
</div>
<div style="display: flex; justify-content: center;">
  <br>
     <input type="submit" class="search_button" style="padding:2.5px 10px" name="submit_password" value="Sign In">
</div>
            </form>
        </div>
    </div>
</div>
