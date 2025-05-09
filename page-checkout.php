<?php
/*
Template Name: Thanh toán
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

<section class="bg-white text-gray-900 font-sans">
  <div class="max-w-7xl mx-auto px-4 py-10">
    <div class="flex flex-col lg:flex-row gap-10">
      <!-- Left form -->
      <form class="flex-1 max-w-3xl" novalidate>
        <h2 class="text-sm font-semibold mb-1">THÔNG TIN THANH TOÁN</h2>
        <a href="#" class="text-xs text-blue-700 mb-6 inline-block">+ Lấy địa chỉ mua hàng trước</a>

        <div class="mb-6">
          <label for="fullname" class="block text-xs font-semibold mb-1">HỌ VÀ TÊN <span class="text-red-600">*</span></label>
          <input
            id="fullname"
            name="fullname"
            type="text"
            placeholder="Họ tên của bạn"
            class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs placeholder:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-600"
            required
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
          <div>
            <label for="phone" class="block text-xs font-semibold mb-1">SỐ ĐIỆN THOẠI <span class="text-red-600">*</span></label>
            <input
              id="phone"
              name="phone"
              type="tel"
              placeholder="Số điện thoại của bạn"
              class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs placeholder:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-600"
              required
            />
          </div>
          <div>
            <label for="email" class="block text-xs font-semibold mb-1">ĐỊA CHỈ EMAIL <span class="text-red-600">*</span></label>
            <input
              id="email"
              name="email"
              type="email"
              placeholder="Email của bạn"
              class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs placeholder:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-600"
              required
            />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
          <div>
            <label for="city" class="block text-xs font-semibold mb-1">TỈNH/THÀNH PHỐ <span class="text-red-600">*</span></label>
            <select
              id="city"
              name="city"
              class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-600"
              required
            >
              <option>Hồ Chí Minh</option>
            </select>
          </div>
          <div>
            <label for="district" class="block text-xs font-semibold mb-1">QUẬN/HUYỆN <span class="text-red-600">*</span></label>
            <select
              id="district"
              name="district"
              class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-600"
              required
            >
              <option>Huyện Cần Giờ</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
          <div>
            <label for="ward" class="block text-xs font-semibold mb-1">XÃ/PHƯỜNG <span class="text-red-600">*</span></label>
            <select
              id="ward"
              name="ward"
              class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs text-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-600"
              required
            >
              <option>Chọn xã/phường</option>
            </select>
          </div>
          <div>
            <label for="address" class="block text-xs font-semibold mb-1">ĐỊA CHỈ <span class="text-red-600">*</span></label>
            <input
              id="address"
              name="address"
              type="text"
              placeholder="Ví dụ: Số 20, ngõ 90"
              class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs placeholder:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-600"
              disabled
              required
            />
          </div>
        </div>

        <div class="mb-6 flex items-center gap-2">
          <input id="new-account" name="new-account" type="checkbox" class="w-4 h-4 border border-gray-400 rounded-sm" />
          <label for="new-account" class="text-xs font-semibold select-none">TẠO TÀI KHOẢN MỚI?</label>
        </div>

        <div class="mb-6 flex items-center gap-2">
          <input id="diff-address" name="diff-address" type="checkbox" class="w-4 h-4 border border-gray-400 rounded-sm" />
          <label for="diff-address" class="text-xs font-semibold select-none">GIAO HÀNG TỚI ĐỊA CHỈ KHÁC?</label>
        </div>

        <div class="mb-6">
          <label for="order-note" class="block text-xs font-semibold mb-1">GHI CHÚ ĐƠN HÀNG (TUỲ CHỌN)</label>
          <textarea
            id="order-note"
            name="order-note"
            rows="3"
            placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."
            class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs placeholder:text-gray-300 resize-none focus:outline-none focus:ring-1 focus:ring-blue-600"
          ></textarea>
        </div>
      </form>

      <!-- Right summary -->
      <aside class="w-full max-w-sm border border-gray-200 p-6 text-xs text-gray-700">
        <button
          type="button"
          class="w-full flex justify-between items-center font-semibold border-b border-gray-300 pb-2 mb-4"
          aria-expanded="false"
          aria-controls="discount-section"
        >
          <span>NHẬP MÃ GIẢM GIÁ</span>
          <i class="fas fa-plus text-xs"></i>
        </button>

        <div class="border-b border-gray-300 pb-4 mb-4">
  <h3 class="font-bold mb-2">ĐƠN HÀNG CỦA BẠN</h3>
  <div id="cart-items-container" class="mb-2 space-y-1"></div>

  <div class="flex justify-between border-t border-gray-300 pt-2 mb-2">
    <p class="font-bold">TẠM TÍNH</p>
    <p class="font-bold" id="subtotal">0₫</p>
  </div>
  <div class="flex justify-between border-t border-gray-300 pt-2 mb-2">
    <div>
      <label class="inline-flex items-center cursor-pointer" for="shipping-now">
        <input
          id="shipping-now"
          name="shipping"
          type="radio"
          checked
          class="form-radio text-blue-600"
        />
        <span class="ml-2">Vận Chuyển Ngay</span>
      </label>
      <p class="ml-7 mt-1">Ahamove:</p>
    </div>
    <p id="shipping-fee">200,000₫</p>
  </div>
  <div class="flex justify-between border-t border-gray-300 pt-2 font-bold">
    <p>TỔNG</p>
    <p id="total">0₫</p>
  </div>
</div>

        <div class="border-b border-gray-300 pb-4 mb-4">
          <h4 class="font-bold mb-2">PAYMENT METHOD</h4>
          <div class="mb-3">
            <label class="inline-flex items-start cursor-pointer" for="bank-transfer">
              <input
                id="bank-transfer"
                name="payment-method"
                type="radio"
                checked
                class="form-radio text-blue-600 mt-1"
              />
              <div class="ml-2 text-xs leading-tight">
                <p class="font-semibold mb-1">Chuyển khoản ngân hàng</p>
                <p>Tài khoản Ngân hàng:</p>
                <p>+ Số TK: <strong>9288887979</strong></p>
                <p>+ Tên TK: <strong>BUI NGOC SON</strong></p>
                <p>
                  + Tên Ngân hàng: <strong>Vietcombank – Ngân hàng thương mại cổ phần Ngoại thương Việt Nam</strong>
                </p>
                <p>+ CN: Sở giao dịch</p>
                <p class="italic text-gray-500 mt-1">
                  Vui lòng ghi “số điện thoại đặt hàng” trong nội dung thanh toán.
                </p>
              </div>
            </label>
          </div>
          <div class="mb-3">
            <label class="inline-flex items-center cursor-pointer" for="cash">
              <input
                id="cash"
                name="payment-method"
                type="radio"
                class="form-radio text-blue-600"
              />
              <span class="ml-2">Tiền mặt</span>
            </label>
          </div>
          <div>
            <label class="inline-flex items-center cursor-pointer" for="vnpay">
              <input
                id="vnpay"
                name="payment-method"
                type="radio"
                class="form-radio text-blue-600"
              />
              <span class="ml-2">Thanh toán qua VNPAY</span>
            </label>
          </div>
        </div>

        <p class="text-[9px] text-gray-500 mb-4">
          Dữ liệu sẽ được sử dụng để hỗ trợ cải thiện trải nghiệm và các mục đích theo chính sách bảo mật được mô tả ở
          <a href="#" class="underline">chính sách riêng tư</a>.
        </p>

        <div class="mb-6 flex items-center gap-2">
          <input id="agree" name="agree" type="checkbox" class="w-4 h-4 border border-gray-400 rounded-sm" />
          <label for="agree" class="text-[9px] select-none">
            Tôi đã đọc và đồng ý với
            <a href="#" class="underline">điều khoản và điều kiện</a> của website <span class="text-red-600">*</span>
          </label>
        </div>

        <button
          type="submit"
          class="w-full bg-[#144552] text-white font-semibold text-xs py-3 rounded-sm hover:bg-[#0f3640] transition-colors"
        >
          ĐẶT HÀNG
        </button>
      </aside>
    </div>
  </div>
</section>
<?php get_footer(); ?>
<?php wp_footer(); ?>
<script>
  const cartItems = [
    { name: 'Giỏ Quà "Gửi Gắm Niềm Nhớ"', quantity: 1, price: 2500000 },
    { name: 'Hộp Trà Tết An Khang', quantity: 2, price: 750000 }
  ];

  const cartContainer = document.getElementById('cart-items-container');
  const subtotalElem = document.getElementById('subtotal');
  const totalElem = document.getElementById('total');
  const shippingFee = 200000;

  function formatCurrency(value) {
    return value.toLocaleString('vi-VN') + '₫';
  }

  function renderCart() {
    let subtotal = 0;
    cartContainer.innerHTML = '';

    cartItems.forEach(item => {
      const itemTotal = item.quantity * item.price;
      subtotal += itemTotal;

      const div = document.createElement('div');
      div.className = 'flex justify-between';
      div.innerHTML = `
        <p class="flex-1 text-ellipsis overflow-hidden whitespace-nowrap pr-2">
          ${item.name} <span class="font-semibold">× ${item.quantity}</span>
        </p>
        <p class="font-semibold">${formatCurrency(itemTotal)}</p>
      `;
      cartContainer.appendChild(div);
    });

    subtotalElem.textContent = formatCurrency(subtotal);
    totalElem.textContent = formatCurrency(subtotal + shippingFee);
  }

  renderCart();
</script>

</body>
</html>