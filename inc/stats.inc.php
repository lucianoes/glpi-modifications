<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

global $DB, $CFG_GLPI; 

$userid =  $_SESSION['glpiID'];
$profid = $_SESSION['glpiactiveprofile']['id'];
$activeent = $_SESSION['glpiactive_entity'];
$glpient = $_SESSION['glpiactiveentities'];

//select profiles, except default
$query_prof = "SELECT id, name FROM `glpi_profiles` WHERE interface <> 'helpdesk'";
$res_prof = $DB->query($query_prof);

while ($row = $DB->fetch_assoc($res_prof))
{	
	$arr_prof[] = $row['id'] ;
}

//if ($profid == 3 || $profid == 4 || $profid == 7) {
if (in_array($profid,$arr_prof)) {
	
	$entities = Profile_User::getUserEntities($_SESSION['glpiID'], true);
	
	if($activeent != 0 and $profid != "6") {		
		$ent = implode(",",$glpient);
		//$ent = $activeent;
		$entidade = "AND glpi_tickets.entities_id IN (".$ent.")";
		$getuser = "admin";
	}	

	elseif($profid == "6") {				
		$ent = $activeent;
		//$ent = implode(",",$entities);
		$entidade = "AND glpi_tickets.entities_id IN (".$ent.")";
		$getuser = 6;
	}	

	else {				
		//$ent = $activeent;
		$ent = implode(",",$entities);
		$entidade = "AND glpi_tickets.entities_id IN (".$ent.")";
		//$getuser = "AND glpi_users.id IN = " . $userid ."" ;
		$getuser = 0;
	}		
}

else {
  //technician
  $ent = implode(",",$glpient);
  $entidade = "AND glpi_tickets.entities_id IN (".$ent.")";
  $getuser = "AND glpi_users.id IN = " . $userid ."" ;
}


//total de chamados abertos
$sql_geral =	"SELECT COUNT(glpi_tickets.id) as total
      FROM glpi_tickets      
      WHERE glpi_tickets.is_deleted = '0'
      AND glpi_tickets.status NOT IN (6)
      ".$entidade." ";

$result_geral = $DB->query($sql_geral) or die ("erro");
$total_geral = $DB->result($result_geral,0,'total');


//total de chamados novos
$sql_new =	"SELECT COUNT(glpi_tickets.id) as total
      FROM glpi_tickets      
      WHERE glpi_tickets.is_deleted = '0'
      AND glpi_tickets.status  = 1
      ".$entidade." ";

$result_new = $DB->query($sql_new) or die ("erro");
$total_new = $DB->result($result_new,0,'total');


//total de chamados atribuidos
$sql_pro =	"SELECT COUNT(glpi_tickets.id) as total
      FROM glpi_tickets      
      WHERE glpi_tickets.is_deleted = '0'
      AND glpi_tickets.status  NOT IN (1,4,5,6)
      ".$entidade." ";

$result_pro = $DB->query($sql_pro) or die ("erro");
$total_pro = $DB->result($result_pro,0,'total');


//total de chamados solucionados
$sql_solv =	"SELECT COUNT(glpi_tickets.id) as total
      FROM glpi_tickets      
      WHERE glpi_tickets.is_deleted = '0'
      AND glpi_tickets.status = 5
      ".$entidade." ";

$result_solv = $DB->query($sql_solv) or die ("erro");
$total_solv = $DB->result($result_solv,0,'total');


//count due tickets
$sql_due = "SELECT DISTINCT COUNT(glpi_tickets.id) AS due
		FROM glpi_tickets		
		WHERE glpi_tickets.status NOT IN (4,5,6)
		AND glpi_tickets.is_deleted = 0
		AND glpi_tickets.time_to_resolve IS NOT NULL
		AND glpi_tickets.time_to_resolve < NOW()
		".$entidade." ";

$result_due = $DB->query($sql_due);
$total_due = $DB->result($result_due,0,'due');


//total de chamados pendentes
$sql_pend =	"SELECT COUNT(glpi_tickets.id) as total
      FROM glpi_tickets      
      WHERE glpi_tickets.is_deleted = '0'
      AND glpi_tickets.status = 4
      ".$entidade." ";

$result_pend = $DB->query($sql_pend) or die ("erro");
$total_pend = $DB->result($result_pend,0,'total');

//var_dump($sql_pend);

//links para lista de chamados
$href_cham = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&criteria[0][field]=12&criteria[0][searchtype]=equals&criteria[0][value]=notclosed&itemtype=Ticket&start=0";
$href_new  = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&criteria[0][field]=12&criteria[0][searchtype]=equals&criteria[0][value]=1&itemtype=Ticket&start=0";
$href_pro  = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&criteria[0][field]=12&criteria[0][searchtype]=equals&criteria[0][value]=process&itemtype=Ticket&start=0";
$href_solv = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&criteria[0][field]=12&criteria[0][searchtype]=equals&criteria[0][value]=5&itemtype=Ticket&start=0";
$href_pend = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&criteria[0][field]=12&criteria[0][searchtype]=equals&criteria[0][value]=4&itemtype=Ticket&start=0";
$href_due  = $CFG_GLPI["root_doc"]."/front/ticket.php?is_deleted=0&criteria[0][field]=82&criteria[0][searchtype]=equals&criteria[0][value]=1&criteria[1][link]=AND&criteria[1][field]=12&criteria[1][searchtype]=equals&criteria[1][value]=notold&itemtype=Ticket&start=0";

echo '
<style>
	@media screen and (max-width: 767px) {
	  #tab_stats { display:none; }
	}
</style>

  <table id="tab_stats" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; text-align:center; margin:auto; width:90%; margin-bottom:20px; background:#fff; border: 1px solid #ddd; table-layout: fixed;" >
	  	<tr>
	      <td style="border-right: 1px solid #ddd; padding: 5px;"><span><a href='.$href_cham .' style="font-size:22pt; color:#337AB7;">' . $total_geral . '</a> </span> </p><span style="color:#333; font-size:14pt;"> '. _nx('ticket','Opened','Opened',2) . '</span></td>
	      <td style="border-right: 1px solid #ddd;"><span><a href='.$href_new .' style="font-size:22pt; color:#555;">' . $total_new . '</a> </span> </p><span style="color:#333; font-size:14pt;"> '. Ticket::getStatus(1) .' </span></td>
	      <td style="border-right: 1px solid #ddd;"><span><a href='.$href_pro .' style="font-size:22pt; color:#49BF8F;">' . $total_pro . '</a></span> </p><span style="color:#333; font-size:14pt;"> '. __('Processing') . ' </span></td>
	      <td style="border-right: 1px solid #ddd;"><span><a href='.$href_solv .' style="font-size:22pt; color:#000;">' . $total_solv . '</a></span> </p><span style="color:#333; font-size:14pt;"> '. Ticket::getStatus(5) .'</span></td>
	      <td style="border-right: 1px solid #ddd;"><span><a href='.$href_pend .' style="font-size:22pt; color:#FFA830;">' . $total_pend . '</a> </span> </p><span style="color:#333; font-size:14pt;"> '. Ticket::getStatus(4) .' </span></td>
	      <td style="border-right: 0px solid #ddd;"><span><a href='.$href_due .' style="font-size:22pt; color:#D9534F;">' . $total_due . '</a>  </span> </p><span style="color:#333; font-size:14pt;"> '. __('Late') . ' </span></td>
	  </tr>
  </table>';

?>
