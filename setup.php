<?php

function plugin_init_mod() {
  
   global $PLUGIN_HOOKS, $LANG ;
	
	$PLUGIN_HOOKS['csrf_compliant']['mod'] = true;         
   
   $plugin = new Plugin();
   if ($plugin->isInstalled('mod') && $plugin->isActivated('mod')) {

	   Plugin::registerClass('PluginMod', [
	      'addtabon' => ['Config']
	   ]);
	             
	   $PLUGIN_HOOKS['config_page']['mod'] = 'config.php';
	   //$PLUGIN_HOOKS['add_javascript']['mod'] = "scripts/mod.js";
	   $PLUGIN_HOOKS['add_javascript']['mod'][] = "scripts/stats.js";
	   $PLUGIN_HOOKS['add_javascript']['mod'][] = "scripts/ind.js";
 	}                    
}


function plugin_version_mod(){
	global $DB, $LANG;

	return array('name'			   => __('GLPI Modifications'),
					'version' 			=> '1.4.0',
					'author'			   => '<a href="https://github.com/stdonato/glpi-modifications"> Stevenes Donato </b> </a>',
					'license'		 	=> 'GPLv2+',
					'homepage'			=> 'https://github.com/stdonato/glpi-modifications',
					'minGlpiVersion'	=> '9.4.3');
}

function plugin_mod_check_prerequisites(){
     if (GLPI_VERSION >= '9.4.3'){
	     	//if(file_exists('/etc/hosts')){      	
	         return true;
	     	//}
         
     } else {
         echo "GLPI version not compatible need 9.4.3";
     }
}


function plugin_mod_check_config($verbose=false){
	if ($verbose) {
		echo 'Installed / not configured';
	}
	return true;
}


?>
