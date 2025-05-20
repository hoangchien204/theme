<?php
/*
Template Name: Thanh toán
*/

// Xử lý yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // Đảm bảo trả về JSON

    if (!isset($_POST['orderData'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu orderData']);
        exit;
    }

    $order = json_decode(stripslashes($_POST['orderData']), true);
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }

    // Kiểm tra dữ liệu bắt buộc
    if (
        !isset($order['makh']) || !is_numeric($order['makh']) || $order['makh'] <= 0 ||
        !isset($order['tongtien']) || !is_numeric($order['tongtien']) || $order['tongtien'] <= 0 ||
        !isset($order['sdt']) || empty($order['sdt']) ||
        !isset($order['diachi']) || empty($order['diachi']) ||
        !isset($order['cart']) || !is_array($order['cart']) || empty($order['cart'])
    ) {
        echo json_encode(['success' => false, 'message' => 'Thiếu hoặc dữ liệu không hợp lệ']);
        exit;
    }

    global $wpdb;

    $makh = $order['makh'];
    $tongtien = $order['tongtien'];
    $phivanchuyen = $order['phivanchuyen'] ?? 0;
    $ghichu = $order['ghichu'] ?? '';
    $sdt = $order['sdt'];
    $diachi = $order['diachi'];
    $ngaylap = current_time('mysql');

    // Kiểm tra MaKH có tồn tại trong wp_custom_users không
    $user_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM wp_custom_users WHERE id = %d", $makh));
    if (!$user_exists) {
        echo json_encode(['success' => false, 'message' => 'Khách hàng không tồn tại']);
        exit;
    }

    // Bắt đầu giao dịch
    $wpdb->query('START TRANSACTION');

    // Chèn vào wp_hoadon
    $result = $wpdb->insert('wp_hoadon', [
        'MaKH' => $makh,
        'NgayDat' => $ngaylap,
        'NgayCan' => $ngaylap,
        'TongTien' => $tongtien,
        'PhiVanChuyen' => $phivanchuyen,
        'GhiChu' => $ghichu,
        'sdt' => $sdt,
        'diachi' => $diachi,
        'MaTrangThai' => 0,
        'NgayGiao' => '1900-01-01 00:00:00'
    ]);

    if ($result === false) {
        $wpdb->query('ROLLBACK');
        echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu hóa đơn: ' . $wpdb->last_error]);
        exit;
    }

    $mahd = $wpdb->insert_id;

    // Chèn chi tiết hóa đơn và cập nhật tồn kho
    foreach ($order['cart'] as $item) {
        if (
            !isset($item['id']) || !is_numeric($item['id']) ||
            !isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0 ||
            !isset($item['price']) || !is_numeric($item['price'])
        ) {
            $wpdb->query('ROLLBACK');
            echo json_encode(['success' => false, 'message' => 'Dữ liệu sản phẩm không hợp lệ']);
            exit;
        }

        $mahh = $item['id'];
        $soluong = $item['quantity'];

        // Kiểm tra tồn kho
        $stock = $wpdb->get_var($wpdb->prepare("SELECT SoLuongTonKho FROM wp_hanghoa WHERE MaHH = %d", $mahh));
        if ($stock === null || $stock < $soluong) {
            $wpdb->query('ROLLBACK');
            echo json_encode(['success' => false, 'message' => "Sản phẩm {$mahh} không đủ tồn kho"]);
            exit;
        }

        // Chèn vào wp_chitiethd
        $result = $wpdb->insert('wp_chitiethd', [
            'MaHD' => $mahd,
            'MaHH' => $mahh,
            'SoLuong' => $soluong,
            'DonGia' => $item['price'],
            'GiamGia' => isset($item['discount']) ? $item['discount'] : 0
        ]);

        if ($result === false) {
            $wpdb->query('ROLLBACK');
            echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu chi tiết hóa đơn: ' . $wpdb->last_error]);
            exit;
        }

        // Cập nhật tồn kho
        $result = $wpdb->query($wpdb->prepare(
            "UPDATE wp_hanghoa SET SoLuongTonKho = SoLuongTonKho - %d WHERE MaHH = %d",
            $soluong,
            $mahh
        ));

        if ($result === false) {
            $wpdb->query('ROLLBACK');
            echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật tồn kho: ' . $wpdb->last_error]);
            exit;
        }
    }

    // Hoàn tất giao dịch
    $wpdb->query('COMMIT');
    echo json_encode(['success' => true, 'mahd' => $mahd]);
    exit;
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
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
                    <!-- Form HTML không thay đổi -->
                    <h2 class="text-sm font-semibold mb-1">THÔNG TIN THANH TOÁN</h2>
                    <div class="mb-6">
                        <label for="fullname" class="block text-xs font-semibold mb-1">HỌ VÀ TÊN <span class="text-red-600 required-star">*</span></label>
                        <input id="fullname" name="fullname" type="text" placeholder="Họ tên của bạn" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs placeholder:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-600" required />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="phone" class="block text-xs font-semibold mb-1">SỐ ĐIỆN THOẠI <span class="text-red-600 required-star">*</span></label>
                            <input id="phone" name="phone" type="tel" placeholder="Số điện thoại của bạn" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs placeholder:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-600" required />
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-semibold mb-1">ĐỊA CHỈ EMAIL <span class="text-red-600 required-star">*</span></label>
                            <input id="email" name="email" type="email" placeholder="Email của bạn" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs placeholder:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-600" required />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="city" class="block text-xs font-semibold mb-1">TỈNH/THÀNH PHỐ <span class="text-red-600 required-star">*</span></label>
                            <select id="city" name="city" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                                <option>Hồ Chí Minh</option>
                            </select>
                        </div>
                        <div>
                            <label for="district" class="block text-xs font-semibold mb-1">QUẬN/HUYỆN <span class="text-red-600 required-star">*</span></label>
                            <select id="district" name="district" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-600" required>
                                <option>Huyện Cần Giờ</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="address" class="block text-xs font-semibold mb-1">ĐỊA CHỈ <span class="text-red-600 required-star">*</span></label>
                            <input id="address" name="address" type="text" placeholder="Ví dụ: Số 20, ngõ 90" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs placeholder:text-gray-300 focus:outline-none focus:ring-1 focus:ring-blue-600" required />
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="order-note" class="block text-xs font-semibold mb-1">GHI CHÚ ĐƠN HÀNG (TUỲ CHỌN)</label>
                        <textarea id="order-note" name="order-note" rows="3" placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn." class="w-full border border-gray-300 rounded-sm px-3 py-2 text-xs placeholder:text-gray-300 resize-none focus:outline-none focus:ring-1 focus:ring-blue-600"></textarea>
                    </div>
                </form>

                <!-- Right summary -->
                <aside class="w-full max-w-sm border border-gray-200 p-6 text-xs text-gray-700">
                    <!-- Aside HTML không thay đổi -->
                    <div class="border-b border-gray-300 pb-4 mb-4">
                        <h3 class="font-bold mb-2">ĐƠN HÀNG CỦA BẠN</h3>
                        <div id="cart-items-container" class="mb-2 space-y-1"></div>
                        <div class="flex justify-between border-t border-gray-300 pt-2 mb-2">
                            <p class="font-bold">TẠM TÍNH</p>
                            <p class="font-bold" id="subtotal">0₫</p>
                        </div>
                        <div class="flex flex-col border-t border-gray-300 pt-2 mb-2 gap-4">
                            <p class="font-bold">HÌNH THỨC VẬN CHUYỂN</p>
                            <div class="flex justify-between">
                                <div>
                                    <label class="inline-flex items-center cursor-pointer" for="shipping-now">
                                        <input id="shipping-now" name="shipping" type="radio" checked class="form-radio text-blue-600" />
                                        <span class="ml-2">Vận Chuyển Ngay</span>
                                    </label>
                                </div>
                                <p id="shipping-fee-immidiate">200,000₫</p>
                            </div>
                            <div class="flex justify-between">
                                <div>
                                    <label class="inline-flex items-center cursor-pointer" for="shipping-later">
                                        <input id="shipping-later" name="shipping" type="radio" class="form-radio text-blue-600" />
                                        <span class="ml-2">Vận Chuyển Thông Thường</span>
                                    </label>
                                </div>
                                <p id="shipping-fee-later">30,000₫</p>
                            </div>
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
                                <input id="bank-transfer" name="payment-method" type="radio" checked class="form-radio text-blue-600 mt-1" />
                                <div class="ml-2 text-xs leading-tight">
                                    <p class="font-semibold mb-1">Chuyển khoản ngân hàng</p>
                                    <p>Tài khoản Ngân hàng:</p>
                                    <p>+ Số TK: <strong>9288887979</strong></p>
                                    <p>+ Tên TK: <strong>BUI NGOC SON</strong></p>
                                    <p>+ Tên Ngân hàng: <strong>Vietcombank – Ngân hàng thương mại cổ phần Ngoại thương Việt Nam</strong></p>
                                    <p>+ CN: Sở giao dịch</p>
                                    <p class="italic text-gray-500 mt-1">Vui lòng ghi “số điện thoại đặt hàng” trong nội dung thanh toán.</p>
                                </div>
                            </label>
                        </div>
                        <div class="mb-3">
                            <label class="inline-flex items-center cursor-pointer" for="cash">
                                <input id="cash" name="payment-method" type="radio" class="form-radio text-blue-600" />
                                <span class="ml-2">Tiền mặt</span>
                            </label>
                        </div>
                        <div>
                            <label class="inline-flex items-center cursor-pointer" for="vnpay">
                                <input id="vnpay" name="payment-method" type="radio" class="form-radio text-blue-600" />
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
                    <button type="submit" id="dathang" class="w-full bg-[#144552] text-white font-semibold text-xs py-3 rounded-sm hover:bg-[#0f3640] transition-colors">
                        ĐẶT HÀNG
                    </button>
                </aside>
            </div>
        </div>
    </section>

    <?php
    $makh = isset($_COOKIE['custom_user_id']) ? intval($_COOKIE['custom_user_id']) : 0;
    if ($makh <= 0) {
        wp_die('Vui lòng đăng nhập để tiếp tục thanh toán.');
    }
    ?>

    <?php get_footer(); ?>
    <?php wp_footer(); ?>

    <script>
        const cartContainer = document.getElementById('cart-items-container');
        const subtotalElem = document.getElementById('subtotal');
        const totalElem = document.getElementById('total');
        const shippingFee = 200000;

        function calculateShippingFee(country, cityCode, districtCode) {
            if (country === 'vn') {
                if (cityCode == 79) return 30000; // TP.HCM
                if (cityCode == 01) return 35000; // Hà Nội
                return 40000;
            }
            return 50000;
        }

        function formatCurrency(value) {
            return value.toLocaleString('vi-VN') + '₫';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedInfo = JSON.parse(localStorage.getItem('shippingAddress'));
            if (savedInfo) {
                if (savedInfo.fullname) document.getElementById('fullname').value = savedInfo.fullname;
                if (savedInfo.phone) document.getElementById('phone').value = savedInfo.phone;
                if (savedInfo.email) document.getElementById('email').value = savedInfo.email;
                if (savedInfo.address) document.getElementById('address').value = savedInfo.address;
            }
        });

        function getCartItems() {
            try {
                const stored = localStorage.getItem('cart');
                if (!stored) return [];
                return JSON.parse(stored);
            } catch (e) {
                console.error('Lỗi khi đọc cart từ localStorage:', e);
                return [];
            }
        }

        function renderCart() {
            const selectedItems = JSON.parse(localStorage.getItem('selectedItemsForCheckout')) || [];
            let subtotal = 0;
            cartContainer.innerHTML = '';

            if (selectedItems.length === 0) {
                cartContainer.innerHTML = '<p class="text-gray-500">Không có sản phẩm nào được chọn.</p>';
            } else {
                selectedItems.forEach(item => {
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
            }

            subtotalElem.textContent = formatCurrency(subtotal);
            totalElem.textContent = formatCurrency(subtotal + shippingFee);
        }


        renderCart();

        function updateShippingFeeAndTotal() {
            const cityCode = document.getElementById('city').value;
            const districtCode = document.getElementById('district').value;

            const isImmediate = document.getElementById('shipping-now').checked;

            const fee = isImmediate ?
                200000 // Vận chuyển ngay (cố định)
                :
                calculateShippingFee('vn', Number(cityCode), Number(districtCode)); // Vận chuyển thường

            document.getElementById('shipping-fee-immidiate').textContent = formatCurrency(200000);
            document.getElementById('shipping-fee-later').textContent = formatCurrency(calculateShippingFee('vn', Number(cityCode), Number(districtCode)));

            const selectedItems = JSON.parse(localStorage.getItem('selectedItemsForCheckout')) || [];
            const subtotal = selectedItems.reduce((sum, item) => sum + item.quantity * item.price, 0);
            totalElem.textContent = formatCurrency(subtotal + fee);
        }

        document.getElementById('shipping-now').addEventListener('change', updateShippingFeeAndTotal);
        document.getElementById('shipping-later').addEventListener('change', updateShippingFeeAndTotal);



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

            const saved = JSON.parse(localStorage.getItem('shippingAddress'));
            if (saved?.city) {
                citySelect.value = saved.city;
                await loadDistricts(saved.city);
                document.getElementById('district').value = saved.district || '';
            }
            updateShippingFeeAndTotal();
        }

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

        document.getElementById('city').addEventListener('change', function() {
            loadDistricts(this.value);
            updateShippingFeeAndTotal();
        });

        document.addEventListener('DOMContentLoaded', function() {
            loadCities();
        });

        function updateRequiredStars() {
            const fields = [{
                    id: 'fullname',
                    label: 'fullname'
                },
                {
                    id: 'phone',
                    label: 'phone'
                },
                {
                    id: 'email',
                    label: 'email'
                },
                {
                    id: 'city',
                    label: 'city'
                },
                {
                    id: 'district',
                    label: 'district'
                },
                {
                    id: 'address',
                    label: 'address'
                }
            ];

            fields.forEach(field => {
                const input = document.getElementById(field.id);
                const label = input?.closest('div')?.querySelector('label');
                const star = label?.querySelector('.required-star');

                if (input && star) {
                    if (input.value.trim() !== '') {
                        star.style.display = 'none';
                    } else {
                        star.style.display = 'inline';
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', updateRequiredStars);
        ['input', 'change'].forEach(evt => {
            document.addEventListener(evt, function() {
                updateRequiredStars();
            });
        });

        function getCartItems() {
            try {
                const stored = localStorage.getItem('cart');
                if (!stored) return [];
                return JSON.parse(stored);
            } catch (e) {
                console.error('Lỗi khi đọc cart từ localStorage:', e);
                return [];
            }
        }

        document.getElementById('dathang').addEventListener('click', function(e) {
            e.preventDefault();

            const requiredFields = ['fullname', 'phone', 'email', 'city', 'district', 'address'];
            let allFilled = true;
            let errorMessages = [];
            let userInfo = {};

            requiredFields.forEach(id => {
                const field = document.getElementById(id);
                if (!field || field.value.trim() === '') {
                    allFilled = false;
                    field?.classList.add('border-red-500');
                    errorMessages.push(`Vui lòng điền ${getFieldName(id)}.`);
                } else {
                    field?.classList.remove('border-red-500');
                    userInfo[id] = field.value.trim();
                }
            });

            const phoneField = document.getElementById('phone');
            if (phoneField && phoneField.value.trim() !== '') {
                const phoneValue = phoneField.value.trim();
                if (!/^0\d{9}$/.test(phoneValue)) {
                    allFilled = false;
                    phoneField.classList.add('border-red-500');
                    errorMessages.push('Số điện thoại không hợp lệ (phải gồm 10 chữ số, bắt đầu bằng 0).');
                }
            }

            const emailField = document.getElementById('email');
            if (emailField && emailField.value.trim() !== '') {
                const emailValue = emailField.value.trim();
                if (!/^[\w-.]+@([\w-]+\.)+[\w-]{2,4}$/.test(emailValue)) {
                    allFilled = false;
                    emailField.classList.add('border-red-500');
                    errorMessages.push('Email không hợp lệ.');
                }
            }

            const agreeField = document.getElementById('agree');
            if (!agreeField.checked) {
                allFilled = false;
                errorMessages.push('Vui lòng đồng ý với điều khoản và điều kiện.');
            }
            if (!allFilled) {
                alert(errorMessages.join('\n'));
                return;
            }

            localStorage.setItem('lastOrderInfo', JSON.stringify(userInfo));
            const cartItems = JSON.parse(localStorage.getItem('selectedItemsForCheckout')) || [];
            const maKH = <?php echo json_encode($makh); ?>;
            const tongTien = cartItems.reduce((sum, item) => sum + item.quantity * item.price, 0);
            const shippingFeeLater = calculateShippingFee('vn', Number(userInfo.city), Number(userInfo.district));
            const isImmediate = document.getElementById('shipping-now').checked;
            const shippingFeeValue = isImmediate ?
                200000 :
                calculateShippingFee('vn', Number(userInfo.city), Number(userInfo.district));
            const tongTienCuoi = tongTien + shippingFeeValue;



            const orderData = {
                makh: maKH,
                sdt: userInfo.phone,
                diachi: userInfo.address,
                ghichu: document.getElementById('order-note').value.trim() || '',
                tongtien: tongTienCuoi,
                phivanchuyen: shippingFeeValue,
                cart: cartItems
            };

            console.log('Dữ liệu gửi đi:', orderData);

            fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'orderData=' + encodeURIComponent(JSON.stringify(orderData))
                })
                .then(res => {
                    console.log('Mã trạng thái:', res.status);
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error(`HTTP error! Status: ${res.status}, Response: ${text}`);
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    console.log('Phản hồi từ server:', data);
                    if (data.success) {
                        alert('Đặt hàng thành công! Mã hóa đơn: ' + data.mahd);

                        // Lấy danh sách sản phẩm trong giỏ hàng hiện tại từ localStorage
                        let currentCart = getCartItems();

                        // Cập nhật số lượng sau khi đặt hàng
                        orderData.cart.forEach(orderedItem => {
                            const index = currentCart.findIndex(item => item.id === orderedItem.id);
                            if (index !== -1) {
                                // Trừ số lượng sản phẩm đã đặt
                                currentCart[index].quantity -= orderedItem.quantity;

                                // Nếu số lượng còn lại <= 0, xóa khỏi giỏ
                                if (currentCart[index].quantity <= 0) {
                                    currentCart.splice(index, 1);
                                }
                            }
                        });

                        // Lưu lại giỏ hàng đã cập nhật
                        if (currentCart.length > 0) {
                            localStorage.setItem('cart', JSON.stringify(currentCart));
                        } else {
                            localStorage.removeItem('cart');
                        }


                        // Cập nhật giao diện giỏ hàng
                        renderCart();

                        // Chuyển hướng sau khi đặt hàng
                        window.location.href = '/wordpress/Oder';
                    } else {
                        alert('Đặt hàng thất bại: ' + (data.message || 'Lỗi không xác định'));
                    }
                })
                .catch(err => {
                    console.error('Lỗi khi gửi đơn hàng:', err);
                    alert('Đã xảy ra lỗi khi đặt hàng: ' + err.message);
                });
        });

        function getFieldName(id) {
            const map = {
                fullname: 'họ tên',
                phone: 'số điện thoại',
                email: 'email',
                city: 'tỉnh/thành phố',
                district: 'quận/huyện',
                address: 'địa chỉ'
            };
            return map[id] || id;
        }

        window.addEventListener('DOMContentLoaded', () => {
            const savedInfo = localStorage.getItem('lastOrderInfo');
            if (savedInfo) {
                const data = JSON.parse(savedInfo);
                Object.entries(data).forEach(([key, value]) => {
                    const field = document.getElementById(key);
                    if (field) field.value = value;
                });
                console.log('Đã tự động điền thông tin từ localStorage:', data);
            }
        });
    </script>
</body>

</html>