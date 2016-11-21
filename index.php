<?php

include ('model.php');
session_start();


/* Recuperation of a existing session */
if (isset($_SESSION["reservation"]) && !empty($_SESSION['reservation']))
  {
    $reservation = unserialize($_SESSION['reservation']);
  }
else
  {
    $reservation = new Reservation();
  }



/* Page one */
if(!empty($_POST['send']) && empty($_POST['cancel']))
  {


    /* Press next with no values ​​in the boxes */ 
  if (empty($_POST["destination"]) && empty ($_POST["nbr_places"]))
    { 
      $reservation->setDestinationError("Veuillez entrer une destination");
      $reservation->setPlaceError("Veuillez rentrer un nombre supérieur à 0 et inférieur à 10");
      include("page_one.php");
    }

  elseif (!empty($_POST["destination"]) && !empty($_POST["nbr_places"]))
  {
    $reservation->setNbr_places($_POST["nbr_places"]);
    $reservation->setDestination($_POST['destination']);

    if (is_numeric($_POST["nbr_places"]) && $_POST["nbr_places"] < 10 && !is_numeric($_POST["destination"]))
      {
        if (isset($_POST['insurance']))
            {
              $reservation->setCheckbox('checked');
            }
            else
            {
              $reservation->setCheckbox('');
            }
            $reservation->setDestinationError('');
            $reservation->setPlaceError('');
          include("page_two.php");
        }

    elseif (is_numeric($_POST["nbr_places"]) && $_POST["nbr_places"] < 10 && is_string($_POST["destination"]) && ($_POST["destination"]) !=0)
        {
          $reservation->setPlaceError("Veuillez entrer une destination");
          $reservation->setNbr_places($_POST["nbr_places"]);
          $reservation->setPlaceError('');
          include("page_one.php");
         }
      else
        { 
          $reservation->setDestination($_POST['destination']);
          $reservation->setNameError('');
          $reservation->setPlaceError("Veuillez rentrer un nombre supérieur à 0 et inférieur à 10");
          include("page_one.php");
        }
    }

    elseif (empty($_POST["destination"]) && !empty($_POST["nbr_places"]))
    { 
      if (is_numeric($_POST["nbr_places"]) && $_POST["nbr_places"] < 10)
      {
        $reservation->setPlaceError('');
        $reservation->setNbr_places($_POST["nbr_places"]);
      }
      else
      {
        $reservation->setPlaceError("Veuillez rentrer un nombre supérieur à 0 et inférieur à 10");
      }
      $reservation->setDestinationError("Veuillez entrer une destination");
      include("page_one.php");
    }
   else 
   {
    if (is_string($_POST["destination"]))
      {
        $reservation->setNameError('');
        $reservation->setDestination($_POST['destination']);
      }
    else
      {
      $reservation->setDestinationError("Veuillez entrer une destination");
      }
      $reservation->setPlaceError("Veuillez rentrer un nombre supérieur à 0 et inférieur à 10");
      include("page_one.php");
    } 
  }

/* Back to the first page */ 
if (!empty($_POST["return_to_reservation"])&& !empty($_POST['nbr_places']) && !empty($_POST["destination"]) && !empty($_POST['send']))
  {
    include("page_one.php");
  }


/* Page two */
if (!empty($_POST["validation"]) && empty($_POST['return_to_reservation']) && empty($_POST['cancel']))
  {
    if (isset($_POST["ages"]) &&  isset ($_POST["names"])) 
    {
      $errorName = 0;
      $errorAge = 0;
      $reservation->setName($_POST['names']);
      $reservation->setAge($_POST['ages']);

    foreach ($reservation->getName() as $inputName)
    {
      if ($inputName == '')
      {
        $errorName +=1;
      }
    }
    foreach ($reservation->getAge() as $inputAge)
    {
      if ($inputAge == '' || !is_numeric($inputAge) || $inputAge < 0)
      {
        $errorAge +=1;
      }
    }

      if ($errorName == 0 && $errorAge ==0)
    {
      include 'page_three.php';
    }
    else
    {
      if ($errorName !=0  &&  $errorAge ==0)
      {
        $reservation->setNameError('Veuillez entrer un nom pour chaque personne');
        $reservation->setAge($_POST['ages']);
        $reservation->setAgeError('');
        include 'page_two.php';
      }
      elseif ($errorName == 0 && $errorAge !=0)
      {
        $reservation->setAgeError('Veuillez entrer un age supérieur à 0');
        $reservation->setName($_POST['names']);
        $reservation->setNameError('');
        include 'page_two.php';
      }
      /*Names box and ages box are empties */
      else
      {
      $reservation->setNameError('Veuillez entrer un nom pour chaque personne');
      $reservation->setAgeError('Veuillez entrer un age supérieur à 0');
      include 'page_two.php';
      }
    }
    } 
  }
/* Back to the second page */
if (isset($_POST["return_to_detail"])) 
  {
  include ("page_two.php");
  }


/* Page Four */ 
if (!empty($_POST["check"]) && empty($_POST["cancel"])&& empty($_POST["return_to_detail"]))
  {
  include("page_four.php");
  }


/* Cancel reservation, destruction the session */
if (!empty($_POST["cancel"]) && isset($_POST["cancel"]))
  {
    session_destroy();
    unset($reservation);
    include("page_one.php");
  }


/* Save session */
if (isset($reservation))
{
  $_SESSION['reservation'] = serialize($reservation);
}


/* Default page */
if(empty($_POST["send"]) && empty($_POST["validation"]) && empty($_POST["check"]) && empty($_POST["cancel"]) && empty ($_POST["return_to_detail"]) )
  {
    include("page_one.php");
  }
?>