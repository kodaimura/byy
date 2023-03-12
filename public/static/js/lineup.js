const liffId = '1657838706-OK1dZMXG';
const taxRate = parseFloat(document.getElementById('tax_rate').value);
const deliveryFeeThreshold = 1500;
const deliveryFeeLow = 300;
const deliveryFeeHigh = 400;


//商品の注文数カウントダウン
const countDown = (id) => {
    let input = document.getElementById(id);
    input.value = 
    (isNaN(input.value) || input.value == "" || input.value == "0")? 
    0 : Math.round(input.value) - 1;
}

//商品の注文数カウントアップ
const countUp = (id) => {
    let input = document.getElementById(id);
    input.value = 
    (isNaN(input.value) || input.value == "")? 
    1 : Math.round(input.value) + 1
}

//商品数入力モーダルのセットアップ
const setupModal = (product) => {
    document.getElementById("orderCount").value = 0
    document.getElementById("modalProductId").value = product.product_id
    document.getElementById("modalImg").innerHTML = `<img src="/static/img/tmp/${product.img_name}" class="rounded img-fluid">`
    document.getElementById("modalProductName").innerHTML = product.product_name
    document.getElementById("modalProductionArea").innerHTML = `${product.production_area}産`
    document.getElementById("modalUnitQuantity").innerHTML = product.unit_quantity
    document.getElementById("modalUnitPrice").innerHTML = `${product.unit_price}`
    document.getElementById("modalTaxPrice").innerHTML = `${Math.round(product.unit_price * taxRate)}`
}

//商品をカートに追加
const addToCart = () => {
    const product_id = document.getElementById("modalProductId").value
    const product_name = document.getElementById("modalProductName").innerHTML
    const production_area = document.getElementById("modalProductionArea").innerHTML
    const unit_price = document.getElementById("modalUnitPrice").innerHTML
    const order_count = Math.round(document.getElementById("orderCount").value)

    const orders0 = JSON.parse(localStorage.getItem('orders'))
    let orders = []

    let order = {
        "product_id": product_id,
        "product_name": `${product_name}(${production_area})`,
        "unit_price": unit_price,  
        "order_count": order_count
    }

    for (let o of orders0) {
        if (o.product_id == product_id) {
            order.order_count += o.order_count
        } else {
            orders.push(o)
        }
    }

    orders.push(order)

    if (order_count > 0) {
        localStorage.setItem('orders', JSON.stringify(orders))
        displayCircle()

        document.getElementById("modalMessage").innerHTML = 
        `<div class="alert alert-info">カートに追加しました<div>`
        setTimeout(() => {
            document.getElementById("modalClose").click()
            document.getElementById("modalMessage").innerHTML = ""
        }, 700)
    } else {
        document.getElementById("modalMessage").innerHTML = 
        `<div class="alert alert-danger">個数を入力してください<div>`
        setTimeout(() => {
            document.getElementById("modalMessage").innerHTML = ""
        }, 2000)
    }
}

//注文確認モーダルのセットアップ
const setupModal2 = () => {
    const orders = JSON.parse(localStorage.getItem('orders'))

    let contents = 
    `<table class="table">
    <thead>
        <tr><th>商品</th><th>個数</th><th>小計</th></tr>
    </thead>
    <tbody>`

    let priceSum = 0
    for (const order of orders) {
        priceSum += (Math.round(order.unit_price) * Math.round(order.order_count))
        contents += 
        `<tr>
        <td>${order.product_name}</td>
        <td>${order.order_count}</td>
        <td>¥${Math.round(order.unit_price * order.order_count * taxRate)}</td>
        </tr>`
    }

    contents += 
    `</tbody>
    <tfooter>
        <tr>
        <td>合計(税込)</td>
        <td></td>
        <td class="text-danger">¥${Math.round(priceSum * taxRate)}</td>
        </tr>
    </tfooter>
    <table>`

    document.getElementById("modal2Body").innerHTML = contents
}

//配達量の取得(注文料金により変わる)
const getDeliveryFee = (priceSum) => {
    return (priceSum >= deliveryFeeThreshold)? deliveryFeeLow : deliveryFeeHigh
}

//注文確定処理
const finalizeOrder = () => {
    const form = document.forms.modal3Form;
    const receiveTime = form.receive_time.value;
    const howToPay = form.how_to_pay.value;
    const howToReceive = form.how_to_receive.value;
    const address = form.address.value.trim();
    const couponId = form.coupon.value;

    if(liff.isInClient()) {
        liff.init({liffId})
        .then(()=>{
            const accessToken = liff.getAccessToken();
            const orders = JSON.parse(localStorage.getItem('orders'))

            let userName = ''

            liff.getProfile()
            .then(profile => {
                userName = profile.displayName
                let message =
                `お客様氏名: ${userName}\n受け取り時刻: ${receiveTime}\n`
                + `受け取り方法: ${howToReceive}\n受け取り場所: ${address}`
                + `\n\n----------ご注文内容----------\n`

                let priceSum = 0
                let count = 0

                for (const order of orders) {
                    priceSum += (Math.round(order.unit_price) * Math.round(order.order_count))
                    count += Math.round(order.order_count)
                    message += 
                    `${order.product_name} ${order.unit_price}円×${order.order_count}\n`
                }
                const taxPrice = Math.round(priceSum * taxRate)
                let deliveryFee = 0
                let couponDiscountFee = 0

                if (couponId === "8") {
                    count += 1
                    message += `もやし(無料クーポン) x1\n`

                } else if (couponId !== "0") {
                    couponDiscountFee = getCouponDiscountFee(couponId)
                    message += `${getCouponText} -${couponDiscountFee}\n`
                }

                message += `合計点数：${count}点\n`
                message += `合計金額：${priceSum - couponDiscountFee}円\n------------------------------\n`
                if (howToReceive === "配達") {
                    deliveryFee = getDeliveryFee(priceSum)
                    message += `配達料金：${deliveryFee}円（税込）\n`
                }
                message += 
                `お支払い金額: ${taxPrice + deliveryFee - couponDiscountFee}円（税込）\nお支払い方法: ${howToPay}`

                liff.sendMessages([
                {
                    type: 'text',
                    text: message,
                },
                ])
                .then(() => {

                    fetch('wakamiya/orders', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            orders: localStorage.getItem('orders'),
                            access_token: accessToken
                        })
                    })
                    window.alert('注文が完了しました。LINEトークでご確認下さい。');
                    location.reload();
                })
                .catch((error) => {
                    window.alert('申し訳ありません。注文に失敗しました。');
                });
            });
        });
    } else {
        document.getElementById("modal5Message").innerHTML = `<div class="alert alert-danger">注文できません<div>`;
        setTimeout(() => {
            document.getElementById("modal5Message").innerHTML = ""
        }, 3000)
    }
}

//支払・受取設定モーダルのセットアップ
const setupModal3 = () => {
    const form = document.forms.modal3Form;
    form.how_to_pay.value = 
    (localStorage.getItem("how_to_pay") == "PayPay")? "PayPay" : "現金";
    if (localStorage.getItem("how_to_receive") == "配達") {
        form.how_to_receive.value = "配達"
        form.address.value = localStorage.getItem("address")
    } else {
        form.how_to_receive.value = "ドライブスルー"
        form.address.value = ""
    }      
}

//注文変更モーダルのセットアップ
const setupModal4 = () => {
    const orders = JSON.parse(localStorage.getItem('orders'))

    let contents = 
    `<table class="table">
    <thead>
        <tr>
        <th style="display:none;"></th>
        <th>商品</th>
        <th>個数</th>
        <th style="display:none;">
        </th>
        </tr>
    </thead>
    <tbody id="modal4TBody">`

    for (let i = 0; i < orders.length; i++) {
        contents += 
        `<tr>
        <td style="display:none;"><input type="hidden" value="${orders[i].product_id}"></td>
        <td>${orders[i].product_name}</td>
        <td style="white-space:nowrap;">
        <button onclick="countDown('oc${i}')">ー</button>
        <input type="number" id="oc${i}" min="0" max="99" value="${orders[i].order_count}">
        <button onclick="countUp('oc${i}')">＋</button>
        </td>
        <td style="display:none;"><input type="hidden" value="${orders[i].unit_price}"></td>
        </tr>`
    }

    contents += ` </tbody><table>`

    document.getElementById("modal4Body").innerHTML = contents
}

//注文変更モーダルでの注文数変更処理
const updateCart = () => {
    let orders = []
    const table = document.getElementById("modal4TBody")

    for (let row of table.rows) {
        let product_id = row.children[0].children[0].value
        let product_name = row.children[1].textContent
        let order_count = row.children[2].children[1].value
        let unit_price = row.children[3].children[0].value

        let order = {
            "product_id": product_id,
            "product_name": product_name,
            "unit_price": unit_price,
            "order_count": Math.round(order_count)
        }
        if (order_count != 0) {
            orders.push(order)
        }
    }
    localStorage.setItem('orders', JSON.stringify(orders))
    displayCircle()
}

//最終確認モーダルを開く
const openModal5 = () => {
    const form = document.forms.modal3Form;
    const howToPay = form.how_to_pay.value;
    const howToReceive = form.how_to_receive.value;
    const address = form.address.value.trim();

    if (howToReceive == "配達" && address == "") {
        document.getElementById("modal3Message").innerHTML = 
        `<div class="alert alert-danger">配達の場合は住所を入力してください<div>`;
        form.address.focus();
        setTimeout(() => {
            document.getElementById("modal3Message").innerHTML = "";
        }, 3000)

    } else {
        const modal3 = bootstrap.Modal.getInstance('#modal3');
        modal3.hide();
        const modal5 = new bootstrap.Modal(document.getElementById('modal5'));
        modal5.show();

        setupModal5();

        localStorage.setItem("how_to_pay", howToPay);
        localStorage.setItem("how_to_receive", howToReceive);
        localStorage.setItem("address", address);
    }
}

//最終確認モーダルのセットアップ
const setupModal5 = () => {
    const orders = JSON.parse(localStorage.getItem('orders'))
    const form = document.forms.modal3Form;

    let contents = 
    `<table class="table">
    <thead>
        <tr><th>商品</th><th>個数</th><th>小計</th></tr>
    </thead>
    <tbody>`

    let priceSum = 0
    let deliveryFee = 0
    let couponDiscountFee = 0

    for (const order of orders) {
        priceSum += Math.round(order.unit_price * order.order_count * taxRate)
        contents += 
        `<tr>
        <td>${order.product_name}</td>
        <td>${order.order_count}</td>
        <td>¥${Math.round(order.unit_price * order.order_count * taxRate)}</td>
        </tr>`
    }

    const couponId = form.coupon.value
    if (couponId !== "0") {
        
        couponDiscountFee = getCouponDiscountFee(priceSum, couponId)
        contents += 
        `<tr>
        <td><small class="text-muted">${getCouponText(couponId)}</small></td>
        <td>${(couponId === "8")? "1" : ""}</td>
        <td>${(couponId === "8")? "¥0" : -couponDiscountFee}</td>
        </tr>`
    }

    if (form.how_to_receive.value === "配達") {
        deliveryFee = getDeliveryFee(priceSum)
        contents += 
        `<tr>
        <td><small class="text-muted">配達料</small></td>
        <td></td>
        <td>¥${deliveryFee}</td>
        </tr>`
    }

    contents += 
    `</tbody>
    <tfooter>
        <tr>
        <td>合計(税込)</td>
        <td></td>
        <td class="text-danger">¥${priceSum + deliveryFee - couponDiscountFee}</td>
        </tr>
    </tfooter>
    <table>`

    document.getElementById("modal5Body").innerHTML = contents
}

//注文が入った時のマークの表示
const displayCircle = () => {
    document.getElementById("circle").innerHTML = 
    (localStorage.getItem('orders') != "[]")? 
    `<div class="circle-inside bg-success"></div>` : "";
}

//配達説明の設定
const setDeliveryConfirm = () => {
    let howToReceiveSelect = document.getElementById("how_to_receive");
    let confirmMsg = 
    `エリア：東区限定\n配達料金：${deliveryFeeLow}円\n`
    confirmMsg += `(お買上げが${deliveryFeeThreshold}円未満の場合 ${deliveryFeeHigh}円)\n`
    confirmMsg += `また、配達時間の指定はできません。`

    //配達が選択された時の処理
    howToReceiveSelect.addEventListener('change', () => {
        if (howToReceiveSelect.selectedIndex == 1) {
            if (!confirm(confirmMsg)) {
                howToReceiveSelect.selectedIndex = 0;
            }
        }
    });
}

//カルーセルの高さを設定
const setCarouselHeight = () => {
    const items = document.getElementsByClassName('carousel-item');
    let maxHeight = 0;

    for (const item of items) {
        if (item.offsetHeight > maxHeight) {
            maxHeight = item.offsetHeight;
        }
    }
    document.getElementById('recommendCarousel').style.height = `${maxHeight + 20}px`;
}


//クーポンIDからクーポンのテキストを取得
const getCouponText = (couponId) => {
    switch (couponId) {
    case "1": 
        return "30%OFFクーポン";
    case "2":
        return "20%OFFクーポン";
    case "3": 
        return "10%OFFクーポン";
    case "4": 
        return "15%OFFクーポン";
    case "5": 
        return "8%OFFクーポン";
    case "6":
        return "5%OFFクーポン";
    case "7": 
        return "3%OFFクーポン";
    case "8":
        return "もやし1袋無料";
    case "0":
        return "利用しない";
    default:
        return "-"
    } 
}

//クーポンIDから割引額を算出
const getCouponDiscountFee = (sum, couponId) => {
    switch (couponId) {
    case "1": 
        return Math.round(sum * 0.3);
    case "2":
        return Math.round(sum * 0.2);
    case "3": 
        return Math.round(sum * 0.1);
    case "4": 
        return Math.round(sum * 0.15);
    case "5": 
        return Math.round(sum * 0.08);
    case "6":
        return Math.round(sum * 0.05);
    case "7": 
        return Math.round(sum * 0.03);
    case "8":
        return 0;
    case "0":
        return 0;
    default:
        return 0;
    } 
}

/*
// スマホだとloadイベント使えなかった
window.addEventListener('load', (event) => {
    setCarouselHeigjt();
});
*/


document.addEventListener('readystatechange', (event) => {
    if (document.readyState === 'complete') {
        setCarouselHeight();
    }
})

window.addEventListener('resize', setCarouselHeight);

document.addEventListener('DOMContentLoaded', () => {
    localStorage.setItem('orders', '[]');
    setDeliveryConfirm();

    if(liff.isInClient()){
        liff.init({
            liffId: liffId
        })
        .then(()=>{
            liff.getProfile()
            .then(profile => {
                document.getElementById('hello-customer').innerHTML = 
                `${profile.displayName}様、ご来店ありがとうございます。`
            })

            fetch(`/wakamiya/coupon`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    access_token: liff.getAccessToken()
                })})
            .then(response => response.json())
            .then(data => {
                let cId = data['coupon_id'];
                if (cId !== "0") {
                    let pulldown = document.getElementById("coupon");
                    let option = document.createElement("option");
                    let text = getCouponText(cId);
                    option.label = text;
                    option.text = text;
                    option.value = cId;
                    option.selected = true;
                    pulldown.appendChild(option);
                } 
            }).catch(console.error)
        });
    } else {
        document.getElementById('hello-customer').innerHTML = 
        `WEBブラウザから注文することはできません。`
    }
});