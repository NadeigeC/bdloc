popup = {



	init: function() {

		// Création pop up
		this.overlay = $("<div>", {
			css: {
				position: "fixed",
				top: 0,
				right: 0,
				bottom: 0,
				left: 0,
				backgroundColor: "rgba(0,0,0,0.8)",
				padding: 50,
				display: "none",
				zIndex: 998
			}
		})

		// J'ajoute au DOM
		$("body").append( this.overlay )

		$(document).on("click", "#retour", this.ferme)

	},

	affiche: function(x) {

		// J'ajoute le contenu, puis j'affiche
		this.overlay.append(x).fadeIn()

		popup.addBack()

	},

	ferme: function() {
		console.log("popup ferme")

		// Je fais disparaitre, puis j'efface le contenu
		popup.overlay.fadeOut({
			complete: function() {
				$(this).empty()
			}
		})

		// Prevent Default
		return false

	},

	addBack: function() {

		this.overlay.append($('<a id="retour" class="glyphicon glyphicon-remove"></a>'))
	}
}


/************************
 * 	 Objet principal 	*
 ************************/

app = {

	init: function() {

		// Gestion du formulaire en Ajax
        //$("#formfilter").on("submit", "form", this.myCriteres)

        // init pop up
        popup.init()

        // Affiche ma BD
       	$(".thumbnail").on("click", "a", this.maBd)
       	//$(".thumbnail").on("click", this.maBd)

	},

	myCriteres: function(event) {

		// J'empêche le comportement normal
		event.preventDefault()

		// Mes variables
		var form = this

			//Ma requête Ajax
			$.ajax({
	            url: form.action,
				data: $(form).serialize(),
	            success : function(html){

	            	var dataz = $(form).serialize()
	            	console.log(dataz)

	            	var details = $(html).find(".catalogue").hide()

	            	$(".catalogue").fadeOut({
					complete: function() {
						$(".catalogue").replaceWith( details )
						details.fadeIn()
					}
					})

	           }
	        })
	},

	maBd: function() {

		// je récup la bd
		var bd = this

		// Je vais chercher les détails
		$.ajax({
			url: bd.href,
			success: function(html) {

				var details = $(html).find("#details")

				popup.affiche( details )
			}
		})


		// Prevent Default
		return false
	},

}
/************************
 * 	Chargement du DOM 	*
 ************************/

$(function() {
	app.init()
})