<?php
  mysql_select_db('ffachmp');
 // if ($_GET['action']=="look")
      {    
      $sql = mysql_query("SELECT * FROM tbl_news ORDER BY id DESC LIMIT 0,2");
      $num = mysql_num_rows($sql);
      $i = 0;
	print '<div id="brdwelcome" class="inbox">';
      print ("<table border=0>");
          while ($i < $num)
              {
              $date = mysql_result($sql, $i, "date");
              $titre = mysql_result($sql, $i, "titre");
              $texte = mysql_result($sql, $i, "texte");
              print("<tr><td class='tcl'><strong>$titre [$date]</strong></td></tr>
              <tr><td>$texte</td></tr><tr></tr>");
              $i++;
              }
      print("<br></table></div>");
      }
      
  if ($_GET['action']=="admin" && $_GET['pass']=="pasencorefaitafaire" && empty($_GET['mode']))
      {
      print("<form method=\"post\" action =\"news.php?action=admin&pass=admin&mode=add\">
              <input type=\"text\" name=\"titre_add\">Titre :<br>
              <input type =\"text\" name=\"date_add\">Date :<br>
              <textarea name=\"texte_add\">Texte :</textarea><br>
              <input type=\"submit\" value=\"Envoyer\">");
      }
  elseif ($_GET['action']=="admin" && $_GET['pass']=="pasencorefaitafaire" && $_GET['mode']=="add")
      {        
      $titre = $_POST['titre_add'];
      $date = $_POST['date_add'];
      $texte = $_POST['texte_add'];
      mysql_query("INSERT INTO tbl_news(titre,texte,date) VALUES('$titre','$texte','$date')");
      print ("News ajoutée");
      print "<br>".mysql_error();
      }
  elseif ($_GET['action']=="delete" && $_GET['pass']=="pasencorefaitafaire" && empty($_GET['mode']))
      {
      $idcount1 = 0;    
      $sql = mysql_query("SELECT * FROM tbl_news ORDER BY id");
      $num = mysql_num_rows($sql);
          print ("<table border=\"1\">");
          while ($idcount1 < $num)
          {
          $id2 = mysql_result($sql, $idcount1, "id");
          $title = mysql_result($sql, $idcount1, "titre");
          print ("<tr><td>$id2</td>
                  <td>$title</td></tr>");
          $idcount1++;
          }
          print ("</table>");
          print("<form method=\"post\" action=\"news.php?action=delete&pass=admin&mode=delete\">");
          //$ask = mysql_query("SELECT id FROM tbl_news");
          //$numid = mysql_num_rows($ask);
          //$e = 0;
          print ("<input type=\"text\" name=\"printid\">");        
   
      print ("<input type=\"submit\" value=\"supprimer\">");
   
   
      }
  elseif ($_GET['action']=="delete" && $_GET['pass']=="pasencorefaitafaire" && $_GET['mode']=="delete")
      {
      $idelete = $_POST['printid'];
      print $idelete;
      mysql_query("DELETE FROM tbl_news WHERE id=$idelete");
      print ("<br>news supprimée");
      print "<br>".mysql_error();
      
      }
?>