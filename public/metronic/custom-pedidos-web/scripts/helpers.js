/**
 * Archivo Javascript de Utilidades Front-End
 */

var helpers = {
	/**
	 * Función para cargar y rederizar el botón del chat en la pagina
	 */	
	loadChat: function () {
		//Capturamos el logo o el texto del botón del chat
		var image = (config.chat.text !== null) ? config.chat.text : '<img src="' + config.chat.image + '" title="Chat">';
		//Agregamos al body el botón del chat
		$('body').append('<a id="chatOnline" class="chatOnline" onclick="helpers.openChat();">' + image + '</a>');
	},
	/**
	 * Función para cargar la ventana del chat
	 */
	openChat: function() {
		//Abrimos el chat en un popup
		window.open(config.chat.url, config.chat.name, config.chat.size);
	},
	/**
	 * Función para cargar el icono de la pagina en la pestaña del navegador
	 */
	loadIcon: function () {
		//Agregamos el icono
		$('head').append('<link href="' + config.brand.icon + '" rel="shortcut icon" type="image/x-icon" />');
	},
	/**
	 * Función para cargar el titulo en la pestaña de la ventana
	 */	
	loadTitle: function () {
		//Agregamos el titulo
		$('head').append('<title>' + config.brand.title + '</title>');
	}  
};