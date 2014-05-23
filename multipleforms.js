// Dependances : jQuery
var $j = jQuery.noConflict();


// recupere les variables passes en paramètres dans l'url
jQuery(document).ready(function setUserGroup() {
  var val = getUrlVars()['groupe'];
  var sel = document.getElementById('cb_groupe');
  for(var i = 0, j = sel.options.length; i < j; ++i) {
    if(sel.options[i].innerHTML === val) { 
      sel.selectedIndex = i;
      $j("#cblabcb_groupe").parent().hide();
      break;
    }
  } 
    
  function getUrlVars() {
    var vars = [];
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
      vars[key] = value;
    });

    return vars;
    }
  }
)

// Si ancien etudiant: afficher l'année d'obtention de diplome
jQuery(document).ready(function hideAnnee(){
    if($j("#cb_groupe").val() ==="AncienEtudiant"){
      $j("#cblabcb_anneediplome").parent().show();
      document.getElementById("email").removeAttribute("readonly");
      document.getElementById("cb_anneediplome").setAttribute("required",true);
    }else if ($j("#cb_groupe").val() ==="Etudiant"){
      $j("#cblabcb_anneediplome").parent().hide();
      document.getElementById("email").setAttribute("readonly","readonly");
      document.getElementById("cb_anneediplome").removeAttribute("required");
    }else{
      $j("#cblabcb_anneediplome").parent().hide();
      document.getElementById("email").removeAttribute("readonly");
      document.getElementById("cb_anneediplome").removeAttribute("required");
    }
      $j("#cb_groupe").click(hideAnnee);
  }
)

// Si étudiant, email non éditable
jQuery(document).ready(function emailBlock(){
  if ($j("#cb_groupe").val() ==="Etudiant"){
    $prenom = "prenom";
    $nom = "nom";
    $j("#email").val( $prenom+"."+$nom+"@etud.univ-montp2.fr");

    $j("#firstname").keyup(function () {
      $prenom = $j("#firstname").val();
      $j("#email").val( $prenom+"."+$nom+"@etud.univ-montp2.fr");
    });
    $j("#lastname").keyup(function () {
      $nom = $j("#lastname").val();
      $j("#email").val( $prenom+"."+$nom+"@etud.univ-montp2.fr");
    });
  }
  $j("#cb_groupe").click(emailBlock);
})
