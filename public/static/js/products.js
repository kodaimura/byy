const deleteProduct = (product_id) => {
	if (window.confirm("削除しますか？")) {
		fetch(`products/${product_id}`, {method: 'DELETE'})
		.then(response => {
    		window.location.reload();
  		});
	}
}

const setupModal = (product) => {
	const form = document.forms.updateForm;
	form.product_id.value = product.product_id
	form.product_name.value = product.product_name
	form.category_id.value = product.category_id
	form.production_area.value = product.production_area
	form.unit_quantity.value = product.unit_quantity
	form.unit_price.value = product.unit_price
	form.stock_flg.value = product.stock_flg
	form.recommend_flg.value = product.recommend_flg
	form.display_flg.value = product.display_flg
	form.seq.value = product.seq
	form.comment.value = product.comment

	document.forms.updateImgForm.product_id.value = product.product_id
}

document.getElementById('send').addEventListener('click', () => {
	const form = document.forms.updateForm;
	const product_id = form.product_id.value

	const category_id = form.category_id.value
	const product_name = form.product_name.value
	const production_area = form.production_area.value
	const unit_quantity = form.unit_quantity.value
	const unit_price = form.unit_price.value
	const stock_flg = form.stock_flg.value
	const recommend_flg = form.recommend_flg.value
	const display_flg = form.display_flg.value
	const comment = form.comment.value
	const seq = form.seq.value

	//reload後、現在位置に移動するため
	localStorage.setItem('positionY', window.pageYOffset);
	localStorage.setItem('categoryId', category_id);

	if (isNaN(unit_price)) {
		window.alert('価格には数値を入力して下さい。');

	} else {
		fetch(`products/${product_id}`, {
			method: 'POST',
			headers: {'Content-Type': 'application/json'},
			body: JSON.stringify({
    		category_id, product_name, production_area, unit_quantity, unit_price, 
    		stock_flg, recommend_flg, display_flg, comment, seq
    	})
  	}).then(response => {
  		window.location.reload();
  	}); 
	}
	     
})

const switchShowDetail = () => {
	if (document.getElementById('product_detail').classList.contains("d-none")) {
		document.forms.updateImgForm.classList.remove('d-none');
		document.getElementById('product_detail').classList.remove('d-none');
		document.getElementById('switch-Button').innerHTML = '簡易表示'
	} else {
		document.forms.updateImgForm.classList.add('d-none');
		document.getElementById('product_detail').classList.add('d-none');
		document.getElementById('switch-Button').innerHTML = 'すべて表示'
	}
}

const setCarouselHeigjt = () => {
	const items = document.getElementsByClassName('carousel-item');
	let maxHeight = 0;

	for (const item of items) {
		if (item.offsetHeight > maxHeight) {
			maxHeight = item.offsetHeight;
		}
	}
	document.getElementById('recommendCarousel').style.height = `${maxHeight + 20}px`;
}

document.addEventListener('readystatechange', (event) => {
	if (document.readyState === 'complete') {
		setCarouselHeigjt();

		const positionY = localStorage.getItem('positionY');
		const categoryId = localStorage.getItem('categoryId');
	
		if (categoryId !== null) {
			document.getElementById(`categoryTab-${categoryId}`).click();
			localStorage.removeItem('categoryId');
		}
	
		if (positionY !== null) {
			setTimeout(() => {
				scrollTo(0, positionY);
				localStorage.removeItem('positionY');
			}, 100)
		}
	}
});

window.addEventListener('resize', setCarouselHeigjt);
