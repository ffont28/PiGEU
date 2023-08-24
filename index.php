<?php
session_start();
include('functions.php');
importVariPerHomePage();
if(!isset($_SESSION['waitValue'])) {
    $_SESSION['waitValue'] = 2000;
}
echo $_SESSION['waitValue'];
?>
<!doctype html>
<html lang="it">
<head>
    <title>PiGEU login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

 <!-- UIkit CSS -->
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.16.23/dist/css/uikit.min.css" />

 <!-- UIkit JS -->
 <script src="https://cdn.jsdelivr.net/npm/uikit@3.16.23/dist/js/uikit.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/uikit@3.16.23/dist/js/uikit-icons.min.js"></script>

<!--    <script nonce="2f8ba6fd-3732-4d90-af0a-8e460ff9be87">(function(w,d){!function(bt,bu,bv,bw){bt[bv]=bt[bv]||{};bt[bv].executed=[];bt.zaraz={deferred:[],listeners:[]};bt.zaraz.q=[];bt.zaraz._f=function(bx){return function(){var by=Array.prototype.slice.call(arguments);bt.zaraz.q.push({m:bx,a:by})}};for(const bz of["track","set","debug"])bt.zaraz[bz]=bt.zaraz._f(bz);bt.zaraz.init=()=>{var bA=bu.getElementsByTagName(bw)[0],bB=bu.createElement(bw),bC=bu.getElementsByTagName("title")[0];bC&&(bt[bv].t=bu.getElementsByTagName("title")[0].text);bt[bv].x=Math.random();bt[bv].w=bt.screen.width;bt[bv].h=bt.screen.height;bt[bv].j=bt.innerHeight;bt[bv].e=bt.innerWidth;bt[bv].l=bt.location.href;bt[bv].r=bu.referrer;bt[bv].k=bt.screen.colorDepth;bt[bv].n=bu.characterSet;bt[bv].o=(new Date).getTimezoneOffset();if(bt.dataLayer)for(const bG of Object.entries(Object.entries(dataLayer).reduce(((bH,bI)=>({...bH[1],...bI[1]})),{})))zaraz.set(bG[0],bG[1],{scope:"page"});bt[bv].q=[];for(;bt.zaraz.q.length;){const bJ=bt.zaraz.q.shift();bt[bv].q.push(bJ)}bB.defer=!0;for(const bK of[localStorage,sessionStorage])Object.keys(bK||{}).filter((bM=>bM.startsWith("_zaraz_"))).forEach((bL=>{try{bt[bv]["z_"+bL.slice(7)]=JSON.parse(bK.getItem(bL))}catch{bt[bv]["z_"+bL.slice(7)]=bK.getItem(bL)}}));bB.referrerPolicy="origin";bB.src="/cdn-cgi/zaraz/s.js?z="+btoa(encodeURIComponent(JSON.stringify(bt[bv])));bA.parentNode.insertBefore(bB,bA)};["complete","interactive"].includes(bu.readyState)?zaraz.init():bt.addEventListener("DOMContentLoaded",zaraz.init)}(w,d,"zarazData","script");})(window,document);</script></head>
-->
<body class="img js-fullheight" style="background-image: url(images/unimi.jpg);">
<?php
if(array_key_exists('logout', $_POST)) {
    $_SESSION['username'] = "";
    $_SESSION['password'] = "";

    echo '<script>alert("LOGOUT EFFETTUATO CON SUCCESSO")</script>';

    unset($_SESSION['username']);
    unset($_SESSION['password']);
    session_destroy();
        }
// Start the session
session_start();
?>

<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center sansation">
            <div class="col-md-6 text-center mb-5">
                <h1 class="bigtitle"> <b>PiGEU</b></h1>
                <h4>piattaforma di gestione per esami universitari</h4>
                <div style="color: #1b1e21">powered by
                    <img src="/images/gothicF.png" width="40" alt="FontLogo">
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-wrap p-0">
                    <h3 class="mb-4 text-center">inserisci le tue credenziali per accedere ai servizi universitari</h3>
                    <form action="#" class="signin-form" method= "POST">
                        <div class="form-group">
                            <input style="background-color: rgba(0, 0, 0, 0.7)" type="text" class="form-control" placeholder="Username" name="username" required>
                        </div>
                        <div class="form-group">
                            <input style="background-color: rgba(0, 0, 0, 0.7)" id="password-field" type="password" class="form-control" placeholder="Password" name="password" required>
                            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="form-control btn btn-primary submit px-3">Sign In</button>
                        </div>
                        <div class="form-group d-md-flex">
<!--                            <div class="w-50">-->
<!--                                <label class="checkbox-wrap checkbox-primary">Remember Me-->
<!--                                    <input type="checkbox" checked>-->
<!--                                    <span class="checkmark"></span>-->
<!--                                </label>-->
<!--                            </div>-->
                            <div class="w-50 text-md-right">
                                <a href="restorepwd.php" style="color: #fff">password dimenticata?</a>
                            </div>
                        </div>
                    </form>
                  <!--  <p class="w-100 text-center">&mdash; Or Sign In With &mdash;</p> -->
                    <p class="w-100 text-center"></p>

                        <?php
                          include_once('functions.php');
                          $db = open_pg_connection();
                            if ($db) {
                            ?>
                            <div>
                                <p style="text-align: center">🟢 ONLINE</p>
                            </div>
                            <?php
                            } else {
                                ?>
                                <div>
                                    <p style="text-align: center">🔴 OFFLINE</p>
                                </div>
                                <?php
                            exit;
                            }
                        ?>
                  <!--  PARTEE RISERVATA PER USI FUTURI CON LOGIN TRAMITE SOCIAL NETWORK
                        <div class="social d-flex text-center">
                        <a href="#" class="px-2 py-2 mr-md-1 rounded"><span class="ion-logo-facebook mr-2"></span> Facebook</a>
                        <a href="#" class="px-2 py-2 ml-md-1 rounded"><span class="ion-logo-twitter mr-2"></span> Twitter</a>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</section>


<script src="js/jquery.min.js"></script>
<script src="js/popper.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/v8b253dfea2ab4077af8c6f58422dfbfd1689876627854" integrity="sha512-bjgnUKX4azu3dLTVtie9u6TKqgx29RBwfj3QXYt5EKfWM/9hPSAI/4qcV5NACjwAo8UtTeWefx6Zq5PHcMm7Tg==" data-cf-beacon='{"rayId":"7ede14c73b0c83a3","token":"cd0b4b3a733644fc843ef0b185f98241","version":"2023.7.0","si":100}' crossorigin="anonymous"></script>

<div id="popup" class="popup">
    <div class="popup-content">
        <h2 style="text-align: center">⛔ ACCESSO NEGATO ⛔</h2>
        <p style="text-align: center" id="popup-text"></p>
    </div>
</div>


</body>
</html>
<?php
if($_SERVER['REQUEST_METHOD']=='POST'){
    include_once('functions.php');

    $db = open_pg_connection();
    $username= strtolower($_POST['username']);
    $password = md5($_POST['password']);
    $params = array($username, $password);
    $sql = "SELECT FROM credenziali WHERE password = $2 AND username = $1";
    $result = pg_prepare($db, 'checkauth', $sql);

    $result = pg_execute($db, 'checkauth', $params);
        if (pg_num_rows($result) == 0) {
            ?>
            <script>
                const popup = document.getElementById('popup');
                const popupText = document.getElementById('popup-text');
                popupText.textContent = 'NOME UTENTE o PASSWORD NON CORRETTI';
                popup.classList.add('active');
                setTimeout(function() {
                    popup.classList.remove('active');
                }, <?php echo $_SESSION['waitValue']; ?>); // Utilizza il valore corrente di waitValue
                <?php
                $value = $_SESSION['waitValue'] / 1000;
                $_SESSION['waitValue'] = $_SESSION['waitValue'] * 2;
                 ?>
            </script>
            <?php
//            sleep($value);
            echo $_SESSION['waitValue'];
        } else { ?>
            <form id="postForm" method="post" action="loggedin.php">
        <input type="hidden" name="username" value="<?php echo $username ?>">
        <input type="hidden" name="password" value="<?php echo $password ?>">
        <button type="submit" style="display: none;"></button>
    </form>

    <script>
            // Sottometti automaticamente il form quando la pagina si carica
            window.onload = function() {
                document.getElementById('postForm').submit();
            };
    </script>
 <?php }
}
?>