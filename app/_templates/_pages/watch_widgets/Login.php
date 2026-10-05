          <div class="you_wnt" id="login_box" style="float:left;width:56%;margin-bottom:8px">
        <div>
            <form action="/login" method="POST">
           
        
            <table cellpadding="3">
                <tr>
                    <input type="hidden" name="jf1" value="acnla">
                    <td align="right"><label for="lg_username">Username:</label></td>
                    <td align="left"><input type="text" name="v_username" id="lg_username" value="" required></td>
                </tr>
                <tr>
                    <td align="right"><label for="password">Password:</label></td>
                    <td align="left"><input id="password" name="password" type="password" value="" required></td>
                </tr>
          <tr>
                        <td></td>
                        <td align="left">
                            <div style="margin-bottom: 8px">
                                <input type="checkbox" id="remember" name="remember" checked="">
                                <label for="remember">Remember Me</label>
                            </div>
                            <input type="submit" class="search_button" style="padding:2.5px 10px" name="submit_login" value="Sign In">
                        </td>
                    </tr>
            </table>
          
            </form>
        </div>
    </div>               <div class="you_wnt" style="float:right;width:240px;margin-bottom:8px;">
                  <div>
                     <strong> No Account?</strong><br>
                    <strong><a href="/register">Sign Up for a free LiiVid Account!</a></strong>
                  </div>
               </div>
       
               <div class="you_wnt" style="float:right;width:240px">
                         <div class="log_fgt"><a href="javascript:void(0)" onclick="alert('You can also log in with your email instead of your username!')">Forgot Username</a> | <a href="/forgot_password">Forgot Password</a></div>
               </div>