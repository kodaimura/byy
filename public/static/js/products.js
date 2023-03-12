//商品削除
const deleteProduct = (product_id) => {
	if (window.confirm("削除しますか？")) {
		fetch(`products/${product_id}`, {method: 'DELETE'})
		.then(response => {
    		window.location.reload();
  		});
	}
}

//更新モーダルのセットアップ
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
	form.recommend_seq.value = product.recommend_seq
	form.comment.value = product.comment

	switchShowSeqRecommendSeq(product.recommend_flg);
	document.forms.updateImgForm.product_id.value = product.product_id
}

//おすすめ商品か通常商品かで更新モーダルに表示する項目(ソート)を切り替える
const switchShowSeqRecommendSeq = (recommend_flg) => {
	if (recommend_flg == '0') {
		if (document.getElementById('seq_wrap').classList.contains("d-none")) {
			document.getElementById('seq_wrap').classList.remove('d-none');
		}
		if (!document.getElementById('recommend_seq_wrap').classList.contains("d-none")) {
			document.getElementById('recommend_seq_wrap').classList.add('d-none');
		}
	} else {
		if (document.getElementById('seq_wrap').classList.contains("d-none")) {
			document.getElementById('seq_wrap').classList.remove('d-none');
		}
		if (document.getElementById('recommend_seq_wrap').classList.contains("d-none")) {
			document.getElementById('recommend_seq_wrap').classList.remove('d-none');
		}
	}
}

//更新モーダルでおすすめと通常を切り替えた際の処理
document.forms.updateForm.recommend_flg.addEventListener('change', (event) => {
	switchShowSeqRecommendSeq(event.target.value);
});

//更新モーダル更新ボタン押下時
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
	const recommend_seq = form.recommend_seq.value

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
    		stock_flg, recommend_flg, display_flg, comment, seq, recommend_seq
    	})
  	}).then(response => {
  		window.location.reload();
  	}); 
	}
	     
})

//更新モーダルで詳細表示と簡易表示を切り替える
const switchShowDetail = () => {
	if (document.getElementById('product_detail').classList.contains("d-none")) {
		document.getElementById('product_detail').classList.remove('d-none');
		document.getElementById('switch-Button').innerHTML = '簡易表示'
	} else {
		document.getElementById('product_detail').classList.add('d-none');
		document.getElementById('switch-Button').innerHTML = 'すべて表示'
	}
}

//カルーセルの高さを要素の一番高いものに合わせる
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

//画面の読み込み状況によるイベント処理
//全て読み込んだ段階でカルーセルの高さを決定する
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

//画面の幅が変わるたびにカルーセルの高さを変更する
window.addEventListener('resize', setCarouselHeigjt);
