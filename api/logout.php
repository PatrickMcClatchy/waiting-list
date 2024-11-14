<?php
session_start();
session_unset();
session_destroy();

// Redirect to the logged out page
header('Location: logged_out.html');
exit;