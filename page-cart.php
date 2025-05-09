<?php
/*
Template Name: Giỏ Hàng
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php get_header(); ?>
<?php get_template_part("Templates/common-banner"); ?>

<section class="bg-white font-sans text-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col md:flex-row md:space-x-20">
    <!-- Left side: Product list -->
    <div class="flex-1">
      <div class="grid grid-cols-6 gap-4 text-gray-500 text-xs uppercase font-normal mb-6 items-center">
  <div class="flex justify-center">
    <input type="checkbox" id="select-all" class="accent-teal-600 w-4 h-4">
  </div>
  <div class="col-span-2">Sản phẩm</div>
  <div class="col-span-2 flex justify-center">Số lượng</div>
  <div class="text-right">Tạm tính</div>
</div>


      <div id="cart-items"></div>

      <div class="mt-4">
  <button onclick="deleteSelectedItems()" class="text-red-600 hover:underline text-sm font-medium">
    <i class="fas fa-trash-alt mr-1"></i> Xóa sản phẩm đã chọn
  </button>
</div>

    </div>

    <!-- Right side: Summary box -->
    <div class="w-full max-w-sm border border-gray-200 p-6 mt-10 md:mt-0">
      <div class="flex justify-between items-center font-bold text-sm uppercase text-gray-900 mb-3 cursor-pointer select-none">
        <span>Nhập mã giảm giá</span>
        <i class="fas fa-plus text-gray-900"></i>
      </div>
      <hr class="border-gray-200 mb-6"/>

      <div class="flex justify-between items-center font-bold text-base mb-6">
        <span>Tạm tính</span>
        <span id="subtotal">0₫</span>
      </div>

      <div class="mb-6">
  <div class="font-bold text-sm mb-2">Giao hàng</div>
  <div>
    <select id="country" class="w-full mb-2 border border-gray-300 px-3 py-2 text-sm">
  <option value="vn">Việt Nam</option> <!-- mặc định là Việt Nam -->
</select>
<select id="city" class="w-full mb-2 border border-gray-300 px-3 py-2 text-sm">
  <option value="">Chọn thành phố</option>
</select>
<select id="district" class="w-full mb-2 border border-gray-300 px-3 py-2 text-sm">
  <option value="">Chọn quận/huyện</option>
</select>

    <button onclick="updateShipping()" class="mt-2 w-full bg-gray-800 text-white py-2 text-sm hover:bg-gray-700">
      Cập nhật địa chỉ
    </button>
  </div>
</div>
 <div class="font-bold text-sm mb-6">
  Phí giao hàng tạm tính:
  <span id="shipping-fee">0₫</span>
</div>

      <hr class="border-gray-200 mb-6"/>

      <div class="flex justify-between items-center font-extrabold text-lg mb-6">
        <span>Tổng</span>
        <span id="total">0₫</span>
      </div>

      <button id="checkout-btn" class="w-full bg-teal-900 hover:bg-teal-800 text-white font-semibold py-3 text-sm tracking-wide" type="button">
        Thanh toán
      </button>
    </div>
  </div>
</section>

<?php get_footer(); ?>
<?php wp_footer(); ?>

<script>
function formatCurrency(amount) {
      return amount.toLocaleString('vi-VN') + '₫';
    }

    function loadCart() {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      const container = document.getElementById('cart-items');
      const subtotalEl = document.getElementById('subtotal');
      const totalEl = document.getElementById('total');
      let subtotal = 0;
      let shipping = parseInt(localStorage.getItem('shippingFee')) || 0;
      const shippingEl = document.getElementById('shipping-fee');
if (shippingEl) shippingEl.textContent = formatCurrency(shipping);

      if (cart.length === 0) {
        container.innerHTML = '<p class="text-gray-500">Không có sản phẩm nào trong giỏ.</p>';
        subtotalEl.textContent = '0₫';
        totalEl.textContent = '0₫';
        return;
      }

      container.innerHTML = cart.map((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        return `
  <div class="grid grid-cols-6 gap-4 items-center border-t border-gray-200 pt-6 pb-4">
    <div class="col-span-1 flex justify-center">
      <input type="checkbox" class="delete-checkbox" data-index="${index}">
    </div>
    <div class="col-span-2 flex items-center space-x-4">
      <img src="${item.image}" alt="${item.name}" class="w-14 h-14 object-cover" />
      <div>
        <div class="text-base font-normal text-gray-900 leading-tight">${item.name}</div>
        <div class="text-xs text-gray-400 mt-1">${item.quantity} x ${formatCurrency(item.price)}</div>
      </div>
    </div>
    <div class="col-span-2 flex justify-center items-center space-x-2">
      <button onclick="updateQty(${index}, -1)" class="border border-gray-300 px-3 py-1 text-lg font-light text-gray-700 hover:bg-gray-100">−</button>
      <div class="w-8 text-center text-base font-normal text-gray-900 select-none">${item.quantity}</div>
      <button onclick="updateQty(${index}, 1)" class="border border-gray-300 px-3 py-1 text-lg font-light text-gray-700 hover:bg-gray-100">+</button>
    </div>
    <div class="col-span-1 text-right font-semibold text-gray-900 text-base">
      ${formatCurrency(itemTotal)}
    </div>
  </div>
`;


      }).join('');

      

      const total = subtotal + shipping;

      subtotalEl.textContent = formatCurrency(subtotal);
      totalEl.textContent = formatCurrency(total);
    }

    function updateQty(index, delta) {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      if (cart[index]) {
        cart[index].quantity += delta;
        if (cart[index].quantity <= 0) {
          cart.splice(index, 1);
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
      }
    }
    function deleteSelectedItems() {
  const checkboxes = document.querySelectorAll('.delete-checkbox:checked');
  if (checkboxes.length === 0) return;

  let cart = JSON.parse(localStorage.getItem('cart')) || [];

  const indexesToDelete = Array.from(checkboxes).map(cb => parseInt(cb.dataset.index)).sort((a, b) => b - a);
  for (let index of indexesToDelete) {
    cart.splice(index, 1);
  }

  localStorage.setItem('cart', JSON.stringify(cart));
  loadCart();
  const selectAll = document.getElementById('select-all');
if (selectAll) {
  selectAll.checked = false;
}

}

    loadCart();
    document.addEventListener('DOMContentLoaded', function () {
  const selectAllCheckbox = document.getElementById('select-all');
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function () {
      const checkboxes = document.querySelectorAll('.delete-checkbox');
      checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    });
  }
});
function calculateShippingFee(country, cityCode, districtCode) {
  if (country === 'vn') {
    if (cityCode == 79) return 30000; // TP.HCM
    if (cityCode == 01) return 35000; // Hà Nội
    return 40000;
  }
  return 50000;
}

async function loadCities() {
  const res = await fetch('https://provinces.open-api.vn/api/?depth=1');
  const cities = await res.json();
  const citySelect = document.getElementById('city');
  citySelect.innerHTML = '<option value="">Chọn thành phố</option>';
  cities.forEach(city => {
    const opt = document.createElement('option');
    opt.value = city.code;
    opt.textContent = city.name;
    citySelect.appendChild(opt);
  });

  // Nếu đã lưu địa chỉ, set lại
  const saved = JSON.parse(localStorage.getItem('shippingAddress'));
  if (saved?.city) {
    citySelect.value = saved.city;
    await loadDistricts(saved.city); // load quận/huyện tương ứng
    document.getElementById('district').value = saved.district || '';
  }
}

// Load Quận/Huyện khi chọn Tỉnh/Thành
async function loadDistricts(cityCode) {
  if (!cityCode) return;
  const res = await fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`);
  const data = await res.json();
  const districtSelect = document.getElementById('district');
  districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
  data.districts.forEach(d => {
    const opt = document.createElement('option');
    opt.value = d.code;
    opt.textContent = d.name;
    districtSelect.appendChild(opt);
  });
}

// Khi chọn thành phố → tự động load quận/huyện
document.getElementById('city').addEventListener('change', function () {
  const cityCode = this.value;
  loadDistricts(cityCode);
});

// Gọi khi trang load
document.addEventListener('DOMContentLoaded', async function () {
  await loadCities();
});


function updateShipping() {
  const country = document.getElementById('country').value;
  const city = document.getElementById('city').value;
  const district = document.getElementById('district').value;

  if (!country || !city || !district) {
    alert('Vui lòng chọn đầy đủ quốc gia, thành phố và quận/huyện.');
    return;
  }

  const fee = calculateShippingFee(country, city, district);
  localStorage.setItem('shippingFee', fee);

  const shippingAddress = { country, city, district };
  localStorage.setItem('shippingAddress', JSON.stringify(shippingAddress));

  loadCart();
  showToast("Đã cập nhật địa chỉ giao hàng!");
}


function loadShippingInfo() {
  const savedAddress = JSON.parse(localStorage.getItem('shippingAddress'));
  if (!savedAddress) return;

  document.getElementById('country').value = savedAddress.country;
  document.getElementById('city').value = savedAddress.city;
  document.getElementById('district').value = savedAddress.district;
}
document.addEventListener('DOMContentLoaded', function () {
  // ✅ Gọi hàm khôi phục địa chỉ đã chọn
  loadShippingInfo();

  const selectAllCheckbox = document.getElementById('select-all');
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function () {
      const checkboxes = document.querySelectorAll('.delete-checkbox');
      checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    });
  }
});
function showToast(message) {
  const toast = document.getElementById('toast');
  toast.textContent = message;
  toast.classList.remove('hidden');
  toast.classList.add('animate-fade-in');

  setTimeout(() => {
    toast.classList.add('animate-fade-out');
    setTimeout(() => {
      toast.classList.add('hidden');
      toast.classList.remove('animate-fade-in', 'animate-fade-out');
    }, 500);
  }, 2500);
}


//nút thanh toán
document.getElementById('checkout-btn').addEventListener('click', function () {
  const checkboxes = document.querySelectorAll('.delete-checkbox:checked');
  if (checkboxes.length === 0) {
    alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
    return;
  }

  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const selectedIndexes = Array.from(checkboxes).map(cb => parseInt(cb.dataset.index));

  const selectedItems = selectedIndexes.map(index => cart[index]);
  
  // Lưu vào localStorage để truyền sang trang thanh toán
  localStorage.setItem('selectedItemsForCheckout', JSON.stringify(selectedItems));

  // Chuyển hướng sang trang thanh toán (ví dụ checkout)
  window.location.href = "/wordpress/check-out"; // hoặc "/checkout" tùy theo slug bạn cấu hình
});

</script>
<div id="toast" class="fixed bottom-6 right-6 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg text-sm hidden z-50">
  Đã cập nhật địa chỉ giao hàng!
</div>

</body>
</html>
<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeOut {
  from { opacity: 1; transform: translateY(0); }
  to { opacity: 0; transform: translateY(10px); }
}
.animate-fade-in {
  animation: fadeIn 0.3s ease forwards;
}
.animate-fade-out {
  animation: fadeOut 0.5s ease forwards;
}
</style>

