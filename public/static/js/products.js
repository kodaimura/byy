const liffId = '1657838706-OK1dZMXG';

window.addEventListener('DOMContentLoaded', () => {
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
   })
 } else {
  document.getElementById('hello-customer').innerHTML = 
  `WEBブラウザから購入することはできません。`
 }
});

window.addEventListener('DOMContentLoaded', (event) => {
  localStorage.setItem('order', "[]")
  const positionY = localStorage.getItem('positionY');
  if (positionY !== null) {
    scrollTo(0, positionY);
    localStorage.removeItem('positionY');
  }
});

const countDown = (id) => {
  let input = document.getElementById(id)
  input.value = (isNaN(input.value) || input.value == "" || input.value == "0")? 
    0 : parseInt(input.value) - 1
}

const countUp = (id) => {
  let input = document.getElementById(id)
  input.value = (isNaN(input.value) || input.value == "")? 1 : parseInt(input.value) + 1
}

const setupModal = (product) => {
  document.getElementById("orderQuantity").value = 0
  document.getElementById("modalProductId").value = product.id
  document.getElementById("modalImg").innerHTML = `<img src="/static/img/tmp/${product.img_name}" class="rounded mx-auto d-block">`
  document.getElementById("modalProductName").innerHTML = product.product_name
  document.getElementById("modalProductionArea").innerHTML = `${product.production_area}産`
  document.getElementById("modalQuantity").innerHTML = product.quantity
  document.getElementById("modalPrice").innerHTML = `${product.price}`
  document.getElementById("modalTaxPrice").innerHTML = `${Math.round(product.price * 1.1)}`
}

const addToCart = () => {
  const id = document.getElementById("modalProductId").value
  const name = document.getElementById("modalProductName").innerHTML
  const area = document.getElementById("modalProductionArea").innerHTML
  const price = document.getElementById("modalPrice").innerHTML
  const oq = parseInt(document.getElementById("orderQuantity").value)

  const orders0 = JSON.parse(localStorage.getItem('order'))
  let orders = []

  let order = {
    "id": id,
    "product_name": name,
    "production_area": area,
    "price": price,
    "order_quantity": oq
  }

  for (let o of orders0) {
    if (o.id == id) {
      order.order_quantity += o.order_quantity
    } else {
      orders.push(o)
    }
  }
  
  orders.push(order)

  if (oq > 0) {
    localStorage.setItem('order', JSON.stringify(orders))
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

const setupModal2 = () => {
  const orders = JSON.parse(localStorage.getItem('order'))

  let contents = 
  `<table class="table">
    <thead>
      <tr><th>商品</th><th>個数</th><th>小計</th></tr>
    </thead>
    <tbody>`

  let priceSum = 0
  for (const order of orders) {
    priceSum += (parseInt(order.price) * parseInt(order.order_quantity))
    contents += 
    `<tr>
      <td>${order.product_name} (${order.production_area})</td>
      <td>${order.order_quantity}</td>
      <td>¥${Math.round(order.price * 1.1) * order.order_quantity}</td>
    </tr>`
  }

  contents += 
  ` </tbody>
    <tfooter>
      <tr>
        <td>合計(税込)</td><td></td><td class="text-danger">¥${Math.round(priceSum * 1.1)}</td>
      </tr>
    </tfooter>
  <table>`

  document.getElementById("modal2Body").innerHTML = contents
}

const finalizeOrder = () => {
  const form = document.forms.modal3Form;
  if (form.how_to_receive.value == "配達" && form.address.value.trim() == "") {
    document.getElementById("modal3Message").innerHTML = 
    `<div class="alert alert-danger">配達の場合は住所を入力してください<div>`;
    form.address.focus();
    setTimeout(() => {
      document.getElementById("modal3Message").innerHTML = ""
    }, 3000)

  } else {
    localStorage.setItem("how_to_pay", form.how_to_pay.value)
    localStorage.setItem("how_to_receive", form.how_to_receive.value)
    localStorage.setItem("address", form.address.value.trim())

    if(!liff.isInClient()){
      //liff.init({liffId})
      //.then(()=>{
        const accessToken = "eyJhbGciOiJIUzI1NiJ9.R21OMMpyft6AhuAs5X1JNMr60ifruzW10kPoNPy8Xfj9ka7u1epKvcJ2b-FsovIulRPzatmgI0DbI-msbCYcQdL5Qul6cD-XFyVSGwTG7BhteN5Mcb_xqi3NQMCNTtIc.D6-ON6F7y6Kqq7JsUw42C_Qsia4E1QzzKGllFE2fjkQ"//liff.getAccessToken();
        const orders = JSON.parse(localStorage.getItem('order'))

        let userName = ''
/*
        liff.getProfile()
        .then(profile => {
          userName = profile.displayName
        })

        let message =
        `お客様氏名: ${userName}
         受け取り時刻: ${form.receive_time}
         受け取り方法: ${form.how_to_receive.value}
         受け取り場所: ${form.address.value}
         ----ご注文内容----`

        let priceSum = 0

        for (const order of orders) {
          priceSum += (parseInt(order.price) * parseInt(order.order_quantity))
          message += 
          `${order.product_name} (${order.production_area})  ${order.price}円×${order.order_quantity}`
        }

        message += 
        `----------------
         お支払い金額: ${Math.round(priceSum * 1.1)}円（税込）
         お支払い方法: ${form.how_to_pay.value}
        `

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
              order: localStorage.getItem('order'),
              access_token: accessToken
            })
          })
          window.alert('注文が完了しました。LINEトークでご確認下さい。');
        })
        .catch((error) => {
          window.alert('申し訳ありません。注文に失敗しました。');
        });
      })
*/
        fetch('wakamiya/orders', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
              order: localStorage.getItem('order'),
              access_token: accessToken
            })
          })

    } else {
      document.getElementById("modal3Message").innerHTML = 
      `<div class="alert alert-danger">注文できません<div>`;
      setTimeout(() => {
        document.getElementById("modal3Message").innerHTML = ""
      }, 3000)
    }
  }
}

const setupModal3 = () => {
  const form = document.forms.modal3Form;

  form.how_to_pay.value = (localStorage.getItem("how_to_pay") == "PayPay")? "PayPay" : "現金";
  if (localStorage.getItem("how_to_receive") == "配達") {
    form.how_to_receive.value = "配達"
    form.address.value = localStorage.getItem("address")
  } else {
    form.how_to_receive.value = "ドライブスルー"
    form.address.value = ""
  }
}

const setupModal4 = () => {
  const orders = JSON.parse(localStorage.getItem('order'))

  let contents = 
  `<table class="table">
    <thead>
      <tr><th></th><th>商品</th><th>個数</th><th></th><th></th><th></th></tr>
    </thead>
    <tbody id="modal4TBody">`

  let priceSum = 0
  for (let i = 0; i < orders.length; i++) {
    priceSum += parseInt(orders[i].price)
    contents += 
    `<tr>
      <td><input type="hidden" value="${orders[i].id}"></td>
      <td>${orders[i].product_name} (${orders[i].production_area})</td>
      <td>
      <button onclick="countDown('oq${i}')">ー</button>
      <input type="number" id="oq${i}" min="0" max="99" value="${orders[i].order_quantity}">
      <button onclick="countUp('oq${i}')">＋</button>
      </td>
      <td><input type="hidden" value="${orders[i].product_name}"></td>
      <td><input type="hidden" value="${orders[i].production_area}"></td>
      <td><input type="hidden" value="${orders[i].price}"></td>
    </tr>`
  }

  contents += 
  ` </tbody>
  <table>`

  document.getElementById("modal4Body").innerHTML = contents
}

const cartUpdateAndSetUpModal2 = () => {
  let orders = []

  const table = document.getElementById("modal4TBody")
  for (let row of table.rows) {
    let id = row.children[0].children[0].value
    let oq = row.children[2].children[1].value
    let name = row.children[3].children[0].value
    let area = row.children[4].children[0].value
    let price = row.children[5].children[0].value

    let order = {
      "id": id,
      "product_name": name,
      "production_area": area,
      "price": price,
      "order_quantity": oq
    }
    if (oq != 0) {
      orders.push(order)
    }
  }
  localStorage.setItem('order', JSON.stringify(orders))
  displayCircle()
  setupModal2()
}

const displayCircle = () => {
  document.getElementById("circle").innerHTML = 
  (localStorage.getItem('order') != "[]")? 
  `<div class="circle-inside bg-success"></div>` : "";
}