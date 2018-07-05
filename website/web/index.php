<?php
  session_start(); //Ouverture de la session
  require '../vendor/autoload.php'; // Librairie mongodb
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Logipedia</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="index.css">
  </head>
  <body>
    <nav class="navbar navbar-expand-md bg-dark navbar-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-award"></i> Logipedia</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavbar">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="about/about.php">About</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Add <i class="fas fa-ban"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="#">Axiom</a>
                  <a class="dropdown-item" href="#">Parameter</a>
                  <a class="dropdown-item" href="#">Definition</a>
                  <a class="dropdown-item" href="#">Theorem</a>
                </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <img src="picture/logipedia-jumb.jpg" class="img-fluid image" alt="Responsive image">

    <hr class="my-4">
    <div class="form-group">
      <div class="row">
        <form method="post" class="col-md-12">
          <div class="row">
            <div class="col-md-3 col-sm-3 col-3"> </div>
            <input type="search" name="inp" class="input-sm form-control col-md-5 col-sm-5 col-5" placeholder="Recherche" >
            <button type="submit" name="sub" class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-eye-open"></span> Chercher</button>
          </div>
        </form>
      </div>
    </div>
    <hr class="my-4">

    <div class="container">
      <div class="list-group">
<?php
  $mongo = new MongoDB\Client('mongodb://localhost:27017'); //Acces au SGBD
  try
  {
    $collection = $mongo->logipedia->definitions; //Recherche sur la collection definitions
    if ((isset($_POST['inp']) && isset($_POST['sub'])) || isset($_SESSION['search']) || isset($_GET['thm'])) { // Si une saisie a ete faite
      if(isset($_POST['inp'])){
        $tabInp = explode(" ",$_POST['inp']); // On recupere les mots
      }
      elseif(isset($_GET['thm'])){
        $tabInp = explode(" ",$_GET['thm']);
      }
      else{
        $tabInp = explode(" ",$_SESSION['search']);
      }
      if(sizeof($tabInp)==1){ // Si un mot a ete saisie
        $result = $collection->aggregate([ // Envoie de la requête
          array(
            '$match' => array(
              '$or' => array(
                array("nameID" => $tabInp[0]),
                array("md" => $tabInp[0])
              )
            )
          ),array('$group' => array('_id' => array('md' => '$md', 'nameID' => '$nameID'))), array('$sort' =>array('_id'=>1))]);
        $compt=0;
        $boolea=0;
        $tab=[];
        foreach ($result as $entry) { //affichage des reponses
          if(isset($entry['_id'])){
           $boolea++;
?>
        <a href="theorems/theorems.php?id=<?php echo $compt;?>&collection=definitions" class="list-group-item list-group-item-action text-center list-group-bg-mar">
<?php
            echo '<h4 class="h4-color"><b>'.$entry['_id']['md'].'.'.$entry['_id']['nameID'].'</b></h4>';
?>
        </a>
<?php
            $tab[$compt]=array('md'=>$entry['_id']['md'], 'nameID' => $entry['_id']['nameID']);
            $compt++;
          }
        }
        $result2 = $mongo->logipedia->axiomes->aggregate([ // De meme pour la collection axiomes
          array(
            '$match' => array(
              '$or' => array(
                array("nameID" => $tabInp[0]),
                array("md" => $tabInp[0])
              )
            )
          ),array('$group' => array('_id' => array('md' => '$md', 'nameID' => '$nameID'))), array('$sort' =>array('_id'=>1))]);
        foreach ($result2 as $entry2) {
          if(isset($entry2['_id'])){
            $boolea++;
?>
        <a href="theorems/theorems.php?id=<?php echo $compt;?>&collection=axiomes" class="list-group-item list-group-item-action text-center list-group-bg-mar">
<?php
            echo '<h4 class="h4-color"><b>'.$entry2['_id']['md'].'.'.$entry2['_id']['nameID'].'</b></h4>';
?>
        </a>
<?php
            $tab[$compt]=array('md'=>$entry2['_id']['md'], 'nameID' => $entry2['_id']['nameID']);
            $compt++;
          }
        }
        $result3 = $mongo->logipedia->theoremes->aggregate([ // De meme pour la collection theoremes
          array(
            '$match' => array(
              '$or' => array(
                array("nameID" => $tabInp[0]),
                array("md" => $tabInp[0])
              )
            )
          ),array('$group' => array('_id' => array('md' => '$md', 'nameID' => '$nameID'))), array('$sort' =>array('_id'=>1))]);
        foreach ($result3 as $entry3) {
          if(isset($entry3['_id'])){
            $boolea++;
?>
        <a href="theorems/theorems.php?id=<?php echo $compt;?>&collection=theoremes" class="list-group-item list-group-item-action text-center list-group-bg-mar">
<?php
            echo '<h4 class="h4-color"><b>'.$entry3['_id']['md'].'.'.$entry3['_id']['nameID'].'</b></h4>';
?>
        </a>
          <?php
            $tab[$compt]=array('md'=>$entry3['_id']['md'], 'nameID' => $entry3['_id']['nameID']);
            $compt++;
          }
        }
        $result4 = $mongo->logipedia->parameters->aggregate([ // De meme pour la collection parameters
          array(
            '$match' => array(
              '$or' => array(
                array("nameID" => $tabInp[0]),
                array("md" => $tabInp[0])
              )
            )
          ),array('$group' => array('_id' => array('md' => '$md', 'nameID' => '$nameID'))), array('$sort' =>array('_id'=>1))]);
        foreach ($result4 as $entry4) {
          if(isset($entry4['_id'])){
            $boolea++;
?>
        <a href="theorems/theorems.php?id=<?php echo $compt;?>&collection=parameters" class="list-group-item list-group-item-action text-center list-group-bg-mar">
<?php
            echo '<h4 class="h4-color"><b>'.$entry4['_id']['md'].'.'.$entry4['_id']['nameID'].'</b></h4>';
?>
        </a>
<?php
            $tab[$compt]=array('md'=>$entry4['_id']['md'], 'nameID' => $entry4['_id']['nameID']);
            $compt++;
          }
        }
        if($boolea==0){
          echo '<h3 class="h3-color text-center">There is no result !</h3>';
        }
      }
      else // Si plusieurs mots ont ete saisis
      {
        $inpOne = $tabInp[0]; // on recupere le premier
        $inpTwo = $tabInp[1]; // et le second
        $result = $collection->aggregate([ // On envoie la requete avec le module et l'id
          array(
            '$match' => array(
              '$or' => array(
                array(
                  '$and' => array(
                    array("nameID" => $inpOne),
                    array("md" => $inpTwo)
                  )
                ),
                array(
                  '$and' => array(
                    array("nameID" => $inpTwo),
                    array("md" => $inpOne)
                  )
                ),
              )
            )
          ),array('$group' => array('_id' => array('md' => '$md', 'nameID' => '$nameID'))), array('$sort' =>array('_id'=>1))]);
        $tab=[];
        $compt=0;
        $boolea=0;
        foreach ($result as $entry) { // On affiche le resultat
          if(isset($entry['_id'])){
            $boolea++;
?>
        <a href="theorems/theorems.php?id=<?php echo $compt;?>&collection=definitions" class="list-group-item list-group-item-action text-center list-group-bg-mar">
<?php
            echo '<h4 class="h4-color"><b>'.$entry['_id']['md'].'.'.$entry['_id']['nameID'].'</b></h4>';
?>
        </a>
<?php
            $tab[$compt]=array('md'=>$entry['_id']['md'], 'nameID' => $entry['_id']['nameID']);
            $compt++;
          }
        }
        $result2 = $mongo->logipedia->axiomes->aggregate([ // On envoie la requete dans la collection axiomes
          array(
            '$match' => array(
              '$or' => array(
                array(
                  '$and' => array(
                    array("nameID" => $inpOne),
                    array("md" => $inpTwo)
                  )
                ),
                array(
                  '$and' => array(
                    array("nameID" => $inpTwo),
                    array("md" => $inpOne)
                  )
                ),
              )
            )
          ),array('$group' => array('_id' => array('md' => '$md', 'nameID' => '$nameID'))), array('$sort' =>array('_id'=>1))]);

          foreach ($result2 as $entry2) { // On affiche les resultats
            if(isset($entry2['_id'])){
              $boolea++;
?>
        <a href="theorems/theorems.php?id=<?php echo $compt;?>&collection=axiomes" class="list-group-item list-group-item-action text-center list-group-bg-mar">
<?php
              echo '<h4 class="h4-color"><b>'.$entry2['_id']['md'].'.'.$entry2['_id']['nameID'].'</b></h4>';
?>
        </a>
<?php
              $tab[$compt]=array('md'=>$entry2['_id']['md'], 'nameID' => $entry2['_id']['nameID']);
              $compt++;
            }
          }
          $result3 = $mongo->logipedia->theoremes->aggregate([ // De meme pour theoremes
            array(
              '$match' => array(
                '$or' => array(
                  array(
                    '$and' => array(
                      array("nameID" => $inpOne),
                      array("md" => $inpTwo)
                    )
                  ),
                  array(
                    '$and' => array(
                      array("nameID" => $inpTwo),
                      array("md" => $inpOne)
                    )
                  ),
                )
              )
            ),array('$group' => array('_id' => array('md' => '$md', 'nameID' => '$nameID'))), array('$sort' =>array('_id'=>1))]);

            foreach ($result3 as $entry3) {
              if(isset($entry3['_id'])){
                $boolea++;
?>
        <a href="theorems/theorems.php?id=<?php echo $compt;?>&collection=theoremes" class="list-group-item list-group-item-action text-center list-group-bg-mar">
<?php
                echo '<h4 class="h4-color"><b>'.$entry3['_id']['md'].'.'.$entry3['_id']['nameID'].'</b></h4>';
?>
        </a>
<?php
                $tab[$compt]=array('md'=>$entry3['_id']['md'], 'nameID' => $entry3['_id']['nameID']);
                $compt++;
              }
            }
            $result4 = $mongo->logipedia->parameters->aggregate([ // De meme pour parameters
              array(
                '$match' => array(
                  '$or' => array(
                    array(
                      '$and' => array(
                        array("nameID" => $inpOne),
                        array("md" => $inpTwo)
                      )
                    ),
                    array(
                      '$and' => array(
                        array("nameID" => $inpTwo),
                        array("md" => $inpOne)
                      )
                    ),
                  )
                )
              ),array('$group' => array('_id' => array('md' => '$md', 'nameID' => '$nameID'))), array('$sort' =>array('_id'=>1))]);

              foreach ($result4 as $entry4) {
                if(isset($entry4['_id'])){
                  $boolea++;
?>
        <a href="theorems/theorems.php?id=<?php echo $compt;?>&collection=parameters" class="list-group-item list-group-item-action text-center list-group-bg-mar">
<?php
                  echo '<h4 class="h4-color"><b>'.$entry4['_id']['md'].'.'.$entry4['_id']['nameID'].'</b></h4>';
?>
        </a>
<?php
                  $tab[$compt]=array('md'=>$entry4['_id']['md'], 'nameID' => $entry4['_id']['nameID']);
                  $compt++;
                }
              }
              if($boolea==0){
                echo '<h3 class="h3-color text-center">There is no result !</h3>';
              }
            }
            $_SESSION['tuple']=$tab; // On enregistre les tuples rechercher dans la session et on y accedera grace au compteur
            unset($_SESSION['search']);
          }
          else{
            $compt=0;
            unset($tab);
            unset($result);
            unset($entry);
            $tabCollection=array("definitions","theoremes","parameters","axiomes");
            $collect=$tabCollection[rand(0,3)];
            $collection = $mongo->logipedia->$collect;
            $result = $collection->aggregate([
            [
                     "\$sample" => ["size"=>10]
            ]
            ]);
            foreach ($result as $entry) {
            if(isset($entry['_id'])){
?>
        <a href="theorems/theorems.php?id=<?php echo $compt;?>&collection=<?php echo $collect;?>" class="list-group-item list-group-item-action text-center list-group-item-light">
<?php
                echo '<h4 class="h4-color"><b>'.$entry['md'].'.'.$entry['nameID'].'</b></h4>';
?>
        </a>
                <?php
                      $tab[$compt]=array('md'=>$entry['md'], 'nameID' => $entry['nameID']);
                      $compt++;
                    }
                  }
                  $_SESSION['tuple']=$tab;
            }
?>
      </div>
    </div>
<?php
            }catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e)
            {
              echo "Erreur";
            }
?>

  </body>
</html>
