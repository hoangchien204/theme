var year = 2025;
var targetDate = new Date(`May 25, 2025 00:00:00`);
var the_time = document.querySelector(".the-time");

function countDown() {
  var currentDate = new Date();
  var timeDiff = targetDate - currentDate;

  // Nếu đã qua thời gian đếm ngược thì dừng lại ở 00:00:00
  var totalSeconds = Math.max(0, Math.floor(timeDiff / 1000));

  var days = Math.floor(totalSeconds / 86400);
  var hours = Math.floor((totalSeconds % 86400) / 3600);
  var minutes = Math.floor((totalSeconds % 3600) / 60);
  var seconds = totalSeconds % 60;

  document.querySelector(".day").innerHTML = String(days).padStart(2, '0');
  document.querySelector(".hours").innerHTML = String(hours).padStart(2, '0');
  document.querySelector(".minutes").innerHTML = String(minutes).padStart(2, '0');
  document.querySelector(".second").innerHTML = String(seconds).padStart(2, '0');
}

countDown();
setInterval(countDown, 1000);

// Hiển thị thời gian đếm đến (the_time)
const thangVN = [
  "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4",
  "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8",
  "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
];

the_time.innerHTML = `${thangVN[targetDate.getMonth()]} ${targetDate.getDate()}, ${targetDate.getFullYear()}`;
