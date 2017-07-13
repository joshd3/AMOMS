<?php session_start(); 
    session_unset(); 
    session_destroy(); 
    session_write_close();
    setcookie(session_name(),'',0,'/');
    session_regenerate_id(true);
    unset ($_POST, $_GET);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('logout').className = 'active';
  };
</script>

<?php include "header.php"; ?>




<h3>You are now logged out of the system.</h3>


<?php include "login.php"; ?>
<?php include "footer.php"; ?>
