const liffId = '1657838706-ZMqeNn5l';

const slotrates = document.getElementById('slotrates').value;
const hitRates = slotrates.split(',').map(x => {return x/100});
const hitValues = [777, 808, 831, 888, 222, 111, 555, 333];
const missValues = [775, 779, 805, 223, 221, 112, 110, 546, 554, 334, 331, 553, 220, 102, 324];
let isHit = false;
odometer.innerHTML = 123;

//スロットを回した結果の3桁の整数
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

//スロットのアニメーションが止まった時の処理
odometer.addEventListener('odometerdone', () => {
	document.getElementById('drum_roll_audio').pause();
	document.getElementById('drum_roll_audio').currentTime = 0;
	document.getElementById('drum_roll_end_audio').currentTime = 0;
	document.getElementById('drum_roll_end_audio').play();

	setTimeout(() => {
		if (isHit) {
			document.getElementById('hit_audio').play();
			party.confetti(document.getElementById("odometer"));

			setTimeout(() => {
				alert("🎉おめでとうございます🎉\nクーポンを送信しました。");
			}, 2200);
		} else {
			document.getElementById('miss_audio').play();
			setTimeout(() => {
				alert("ありがとうございます。\nまたの挑戦お待ちしております！");
			}, 1700);
		}
	}, 900);
});

//スロットボタン押下時の処理
document.getElementById('slotbutton').addEventListener("click", async () => {
	let now = new Date();
	const slotLastDate = localStorage.getItem('slot_last_date');
	if (slotLastDate > now.setHours(now.getHours() - 12).toLocaleString()) {
		alert("12時間に一回しか挑戦できません。");
	} else {
		odometer.innerHTML = 123
		document.getElementById('drum_roll_audio').play();
		const result = slot();
		isHit = hitValues.includes(result);
		const couponId = getCouponId(result);

		localStorage.setItem('slot_last_date', (new Date()).toLocaleString());

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

//現在日時を取得
const getFormatDate = () => {
	let now  = new Date();
	let Year = now.getFullYear();
	let Month = now.getMonth()+1;
	let Day = now.getDate();
	let Hour = now.getHours();
	let Min = now.getMinutes();
	
	return  Year + "年" + Month + "月" + Day + "日" + Hour + "時" + Min + "分";
}

//クーポンIDからクーポン用の当たりメッセージを取得
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

//スロットの結果からクーポンIDを取得
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

//クーポンをLINEトークに送信
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
			          "size": "xl",
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
			          "text": "【使い方】ドライブスルー、配達をご利用のお客様は、注文ページでクーポンをご利用下さい。または、レジにてこのクーポンをお見せ下さい。",
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