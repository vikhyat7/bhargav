function myFunction(url) {

var qty = prompt('Enter number of Barcode copies you want to print !!\n ( Between 1 to 500 )', 1);
    if (qty) {

      if(qty >= 1 && qty <= 500){
        var url1 = new URL(url);
        url1.searchParams.set("pages", qty);
        window.location.href = url1;          
      }else
      {
        alert("Enter Number Between 1 to 500");
      }
      
    }
    else{
    alert("Please enter number of pages !!!");
    }

}
