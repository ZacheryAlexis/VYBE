<style>
 form[name="login"] {
   display: grid;
   grid-template-columns: 100px 1fr;
   gap: 10px 5px;
   align-items: center;
   max-width: 300px;
 }
 form[name="login"] label {
   text-align: right;
   padding-right: 5px;
 }
 form[name="login"] input[type="text"],
 form[name="login"] input[type="password"] {
   width: 100%;
 }
 form[name="login"] input[type="submit"] {
   grid-column: 2;
   justify-self: start;
 }
</style>
<?php
if (!isset($_SESSION['login'])) {
}?>
 <h2>Please log in</h2><br>
 <form name="login" action="index.php" method="post">
   <label>Email:</label>
   <input type="text" name="emailAddress" size="20">
   
   <label>Password</label>
   <input type="password" name="password" size="20">
   
   <input type="submit" value="Login">
   <input type="hidden" name="content" value="validate">
 </form>
 <p style="margin-top:12px;">New to Vybe? <a href="index.php?content=newuser">Create an account</a> or <a href="index.php?content=user_login">User login</a>.</p>