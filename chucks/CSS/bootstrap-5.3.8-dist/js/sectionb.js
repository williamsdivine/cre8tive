let colors = ['Orange', 'Green', 'Black', 'Red']
colors.push('yellow')
colors[1]

for (let i = 0; i < colors.length; i++) {
  console.log(colors[i]);
}

let student = {
    name: 'Nuel',
    age: 35,
    grade: 40
}

student.name;
student.grade = 50;

const numbers = [10, 20, 30];

function number(x) {
  if (x.length < 3) {
  }
  return x[0] + x[1] + x[2];
}
console.log(number(numbers));