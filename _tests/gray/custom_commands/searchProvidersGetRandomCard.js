module.exports = {
	command: async function() {
		// searchProvidersGetRandomCard()
		// Selects a randdomly chosen provider card from the current search provider results page.
		console.log("Let's find a random card...");
		this.elements("css selector", "article", function(result){
			ranCard = Math.floor(Math.random() * (result.value.length-1));
			console.log("ranCard="+ranCard);
			return ranCard;
		})
	}
};