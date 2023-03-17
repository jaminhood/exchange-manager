$(document).ready(function () {
	/* === Database Table Scripts === */
	$("#database-table").dataTable({
		columnDefs: [{ sortable: true }],
	});
	/* === Colapsable Scripts === */
	$("[data-collapse]").each(function () {
		const me = $(this),
			target = me.data("collapse");

		me.click(function () {
			$(target).collapse("toggle");
			$(target).on("shown.bs.collapse", function () {
				me.html('<i class="fas fa-minus"></i>');
			});
			$(target).on("hidden.bs.collapse", function () {
				me.html('<i class="fas fa-plus"></i>');
			});
			return false;
		});
	});
	/* === Image Custom Uploader Scripts === */
	const uri = window.location.href;
	const addNews = "admin.php?page=em-top-news&tab=add";
	const updateNews = "admin.php?page=em-top-news&tab=update";
	if (uri.includes(addNews) || uri.includes(updateNews)) {
		const addBtn = document.getElementById("newsImage");
		const newsId = document.getElementById("nwesImageId");
		const newsLabel = document.getElementById("newsImageLabel");
		const newsUploader = wp.media({
			title: "Choose News",
			button: {
				text: "Use this Image",
			},
			multiple: false,
		});
		addBtn.addEventListener(`click`, (e) => {
			e.preventDefault();
			if (newsUploader) {
				newsUploader.open();
			}
		});
		newsUploader.on(`select`, () => {
			const attachment = newsUploader.state().get(`selection`).first().toJSON();
			newsLabel.textContent = attachment.url;
			newsId.setAttribute(`value`, attachment.id);
		});
	}
});
