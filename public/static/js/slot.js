const liffId = '1657838706-OK1dZMXG';

const slotrates = document.getElementById('slotrates').value;
const hitRates = slotrates.split(',').map(x => {return x/100});
const hitValues = [777, 808, 831, 888, 222, 111, 555, 333];
const missValues = [775, 779, 805, 223, 221, 112, 110, 546, 554, 334, 331, 553, 220, 102, 324];
odometer.innerHTML = 888;

const slot = () => {
	x = Math.random();

	const sumf = (sum, num) => sum + num;
	if (x <= hitRates.slice(0,1).reduce(sumf,0)) {
		return hitValues[0];
	} else if (x <= hitRates.slice(0,2).reduce(sumf,0)) {
		return hitValues[1];
	} else if  (x <= hitRates.slice(0,3).reduce(sumf,0)) {
		return hitValues[2];
	} else if  (x <= hitRates.slice(0,4).reduce(sumf,0)) {
		return hitValues[3];
	} else if  (x <= hitRates.slice(0,5).reduce(sumf,0)) {
		return hitValues[4];
	} else if  (x <= hitRates.slice(0,6).reduce(sumf,0)) {
		return hitValues[5];
	} else if  (x <= hitRates.slice(0,7).reduce(sumf,0)) {
		return hitValues[6];
	} else if  (x <= hitRates.slice(0,8).reduce(sumf,0)) {
		return hitValues[7];
	} else {
		return missValues[Math.floor(Math.random() * missValues.length)];
	}
}



document.getElementById('slotbutton').addEventListener("click", async () => {
	const slotDone = localStorage.getItem('slotdone');
	if (slotDone) {
		alert("1日1回までです")
	} else {
		document.getElementById('drum_roll_audio').play();
		const result = slot();
		const isHit = hitValues.includes(result);
		const couponId = getCouponId(result);

		localStorage.setItem('slotdone', true);

		odometer.addEventListener('odometerdone', () => {
			document.getElementById('drum_roll_audio').pause();
			document.getElementById('drum_roll_audio').currentTime = 0;
			document.getElementById('drum_roll_end_audio').play();

			setTimeout(() => {
				if (isHit) {
					document.getElementById('hit_audio').play();
				} else {
					document.getElementById('miss_audio').play();
				}
			}, 800);
		});

		odometer.innerHTML = result;
		
		if(liff.isInClient()) {
        	liff.init({liffId})
        	.then(()=>{
        		if (isHit) {
        			liff.getProfile()
        			.then(profile => {
        				sendCoupon(profile.displayName, getCouponMessage(couponId));
        			});
        		}

        		const accessToken = liff.getAccessToken();
        	    fetch('/wakamiya/coupons', {
        	    	method: 'POST',
        	    	headers: {'Content-Type': 'application/json'},
        	    	body: JSON.stringify({
        	    		coupon_id: couponId,
        	    		access_token: accessToken
        	    	})
        	    }).catch(error => {
        	    	console.error(error)
        	    }); 
        	})
    	}
	}
	
})

const getFormatDate = () => {
	let now  = new Date();
	let Year = now.getFullYear();
	let Month = now.getMonth()+1;
	let Day = now.getDate();
	let Hour = now.getHours();
	let Min = now.getMinutes();
	
	return  Year + "年" + Month + "月" + Day + "日" + Hour + "時" + Min + "分";
}

const getCouponMessage = (couponId) => {
	switch (couponId) {
	case 1: 
		return "合計から 30%OFF!";
	case 2:
		return "合計から 20%OFF!";
	case 3: 
		return "合計から 10%OFF!";
	case 4: 
		return "合計から 15%OFF!";
	case 5: 
		return "合計から 8%OFF!";
	case 6:
		return "合計から 5%OFF!";
	case 7: 
		return "合計から 3%OFF!";
	case 8:
		return "もやし1袋プレゼント!";
	} 
}

const getCouponId = (result) => {
	switch (result) {
	case 777: 
		return 1;
	case 808:
		return 2;
	case 831: 
		return 3;
	case 888: 
		return 4;
	case 222: 
		return 5;
	case 111:
		return 6;
	case 555: 
		return 7;
	case 333:
		return 8;
	default:
		return 0;
	} 
}

const sendCoupon = (username, message) => { 
	if (liff.isInClient()) {
		liff.sendMessages([{
			"type": "flex",
			"altText": "当たりクーポンを送信しました！",
			"contents":
			{
			    "type": "bubble",
			    "header": {
			    	"type": "box",
			    	"layout": "vertical",
			    	"contents": [
			    	{
						"type": "text",
						"text": "バカ安ルーレットクーポン",
						"align": "center",
						"color": "#FFFFFF",
						"weight": "bold"
					}
					],
					"backgroundColor": "#00BB00"
			    },
			    "body": {
			      "type": "box",
			      "layout": "vertical",
			      "contents": [
			        {
			          "type": "spacer"
			        },
			        {
			          "type": "text",
			          "text": username+"様",
			          "align": "center"
			        },
			        {
			          "type": "text",
			          "text": "ご当選おめでとうございます！！",
			          "align": "center",
			          "margin": "lg"
			        },
			        {
			          "type": "text",
			          "text": "㊗️大当たり！㊗️",
			          "size": "xxl",
			          "weight": "bold",
			          "align": "center",
			          "margin": "md"
			        },
			        {
			          "type": "text",
			          "text": message,
			          "size": "xxl",
			          "weight": "bold",
			          "margin": "md",
			          "decoration": "underline",
			          "align": "center"
			        },
			        {
			          "type": "text",
			          "text": "当選時刻："+ getFormatDate(),
			          "margin": "xl",
			          "color": "#ff3300",
			          "size": "md"
			        },
			        {
			          "type": "separator",
			          "margin": "lg"
			        },
			        {
			          "type": "text",
			          "text": "【使い方】レジにてこのクーポンをお見せ下さい。ドライブスルー、配達をご利用のお客様は、注文ページでクーポンを選択して下さい。",
			          "wrap": true,
			          "margin": "md",
			          "size": "sm"
			        },
			        {
			          "type": "text",
			          "text": "【注意】このクーポンの有効期限は当選から12時間です。ご利用は1回限りです。",
			          "wrap": true
			        }
			      ],
			      "backgroundColor": "#ffffff"
			    }
			}
		}]).catch((error) => {
		    alert("申し訳ありません。クーポンのトークへの送信に失敗しました。スロット結果を写真に取りクーポンとしてお使いください。");
		});
    };
}