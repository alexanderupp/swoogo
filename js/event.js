(function() {
	document.querySelector(".btn-explore").addEventListener("click", function(e) {
		e.preventDefault();

		const sessions = document.getElementById("event__sessions");

		window.scrollTo({top: sessions.offsetTop, behavior: "smooth"});
	});

	document.querySelectorAll(".toggle-details").forEach((btn) => {
		btn.addEventListener("click", (e) => {
			e.preventDefault();

			let ID = btn.dataset["for"];

			document.body.classList.toggle("open-details");
			document.getElementById(ID).classList.toggle("show");
		});
	});
})();