window.addEventListener('DOMContentLoaded', () => {
 const liffId = '1657838706-OK1dZMXG';

 if(liff.isInClient()){

   liff.init({
     liffId: liffId
   })
   .then(()=>{
      const idToken = liff.getIDToken();
      localStorage.setItem("liffIdToken", idToken)
   })
 }
});