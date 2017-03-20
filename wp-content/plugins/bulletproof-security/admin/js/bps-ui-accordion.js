/*  ________         ____________      _____ ________                       ________      
#  ___  __ )____  _____  /___  /_____ __  /____  __ \______________ ______ ___  __/      
#  __  __  |_  / / /__  / __  / _  _ \_  __/__  /_/ /__  ___/_  __ \_  __ \__  /_        
#  _  /_/ / / /_/ / _  /  _  /  /  __// /_  _  ____/ _  /    / /_/ // /_/ /_  __/        
#  /_____/  \__,_/  /_/   /_/   \___/ \__/  /_/      /_/     \____/ \____/ /_/           
#  ________                             _____ _____              ________                
#  __  ___/_____ ___________  _____________(_)__  /______  __    ___  __ \______________ 
#  _____ \ _  _ \_  ___/_  / / /__  ___/__  / _  __/__  / / /    __  /_/ /__  ___/_  __ \
#  ____/ / /  __// /__  / /_/ / _  /    _  /  / /_  _  /_/ /     _  ____/ _  /    / /_/ /
#  /____/  \___/ \___/  \__,_/  /_/     /_/   \__/  _\__, /      /_/      /_/     \____/ 
#                                                   /____/                               
# 42756C6C657450726F6F66 5365637572697479 50726F 
*/
// BPS jQuery Accordion
jQuery(document).ready(function($){
    
	$( '#bps-accordion-1' ).addClass( "bps-accordion" );
	$( "#bps-accordion-1" ).accordion({
		collapsible: true,
		autoHeight: false,
		clearStyle: true,
		heightStyle: "content"
    });
    
	// not displayed open by default - slower/smoother animation
	$( '#bps-accordion-2' ).addClass( "bps-accordion" );	
	$( "#bps-accordion-2" ).accordion({
		active: false,
		animate: 1500,
		collapsible: true,
		autoHeight: false,
		clearStyle: true,
		heightStyle: "content"
    });

    // displayed open by default - slower/smoother animation
	$( '#bps-accordion-3' ).addClass( "bps-accordion" );	
	$( "#bps-accordion-3" ).accordion({
		animate: 1400,
		collapsible: true,
		autoHeight: false,
		clearStyle: true,
		heightStyle: "content"
    });

});