const services = [
  {
    name: "Web Development",
    basePrice: 750,
    discount: 20,
    getPrice: function() {
      return this.basePrice - (this.basePrice * this.discount / 100);
    }
  },
  {
    name: "UI Design",
    basePrice: 500,
    discount: 10,
    getPrice: function() {
      return this.basePrice - (this.basePrice * this.discount / 100);
    }
  },
  {
    name: "SEO",
    basePrice: 400,
    discount: 15,
    getPrice: function() {
      return this.basePrice - (this.basePrice * this.discount / 100);
    }
  }
];

let button1 = document.getElementById('btn1');
let button2 = document.getElementById('btn2');
let button3 = document.getElementById('btn3');

button1.addEventListener('click', function() {
    alert{}
})




