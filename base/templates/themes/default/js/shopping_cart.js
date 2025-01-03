document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');

    buttons.forEach(button => {
        button.addEventListener('click', function () {
            // 清除所有按鈕的選擇狀態和內容顯示
            buttons.forEach(btn => btn.classList.remove('active'));
            contents.forEach(content => content.style.display = 'none');

            // 設定當前按鈕為選擇狀態並顯示對應內容
            button.classList.add('active');
            const target = button.getAttribute('data-target');
            document.getElementById(target).style.display = 'block';
        });
    });

    // 設定第一個按鈕為預設選擇
    if (buttons.length > 0) {
        buttons[0].click();
    }

    setupEventHandlers(); // 調用主功能
});


function updateQuantity(element, delta) {
    const currentQty = parseInt(element.siblings('.qty').val()); // 假設你的數量 input 使用 class 'qty'
    const newQty = currentQty + delta;

    if (newQty >= 1) { // 確保數量不會低於 1
        element.siblings('.qty').val(newQty);
    }
}


function setupEventHandlers() {
    const $shoppingCartElements = $('#shopping_cart_amount, #shopping_cart, #order_info_content, #total_prices, #checked_continue');
    const $checkedElements = $('#delivery_options, #order_info_content_checked, #total_prices_checked, #checked_continue_step2');

    // Step 1 繼續
    $('#checked_continue_btn_step1').click(function(e) {
        e.preventDefault();
        $shoppingCartElements.hide();
        $checkedElements.show();
    });

    // Step 2 上一頁
    $('#prepage_step2').click(function(e) {
        e.preventDefault();
        $checkedElements.hide();
        $shoppingCartElements.show();
    });

    // 商品數量按鈕加減
    $('.qtyplus').click(function(e) {
        e.preventDefault();
        updateQuantity($(this), 1);
    });

    $(".qtyminus").click(function(e) {
        e.preventDefault();
        updateQuantity($(this), -1);
    });

    // 監聽第一層配送選擇
    $('input[name="delivery"]').change(function() {
        handleDeliveryChange(); // 處理第一層修改
    });

    // 監聽第二層配送選擇
    $('input[name="delivery_method"]').change(function() {
        const deliveryMethod = $(this).val(); // 獲取當前選擇的配送方式
        // console.log('選擇的配送方式:', deliveryMethod);
        handleDeliveryChange(); // 根據新的選擇更新顯示
    });

    // Step 2 繼續按鈕
    $('#checked_continue_btn_step2').click(function(e) {
        e.preventDefault();
        handleStep2Continue();
    });

    // Step 3 返回按鈕
    $('#prepage_step3').click(function(e) {
        e.preventDefault();
        handleStep3Back();
    });

    $(document).on('change', 'input[name="delivery_method"]', function() {
        const deliveryMethod = $(this).val(); // 獲取當前選擇的配送方式
        // console.log('選擇的配送方式:', deliveryMethod);
    });
    
}




let selectedPaymentOption = ''; // globel記錄選擇的付款與配送方式

function handleDeliveryChange() {
    const isOverseas = $('#overseas_shipping').is(':checked');
    selectedPaymentOption = ''; // 在每次改變時重置

    // 清空現有選項
    $('#credit_card_options, #cash_on_delivery_options, #overseas_shipping_options').empty();

    if (isOverseas) {
        // 海外配送選擇
        $('.t_delivery_checked').text('配送 (商品滿4,000免運)');
        $('.t_delivery_checked_value').text('$ 115');
        $('.checked-info-text').text('USD$ 71.25').addClass('nowrap font-weight-bold text-danger h4_5 pt-1');

        // 設定「海外配送」的付款方式
        selectedPaymentOption = '信用卡付款／宅配到府';
        $('#overseas_shipping_options').append(`
            <div class="d-flex">
                <div class="overseas_country_title">國家</div>
                <select id="overseas_country" name="overseas_country">
                    <option value="hongkong">香港 Hong Kong</option>
                </select>
            </div>
        `);
    } else {
        // 台灣境內配送選擇
        $('.t_delivery_checked').text('配送 (商品滿2,500免運)');
        $('.t_delivery_checked_value').text('$ 85');
        $('.checked-info-text').text('• 含營業稅').removeClass('font-weight-bold text-danger h4_5');

        // 判斷第一層選擇
        if ($('#standard_credit_card').is(':checked')) {
            $('#credit_card_options').append(`
                <label><input class="custom-checkbox" type="radio" name="delivery_method" value="convenience_store" checked> 超商取貨</label>
                <label><input class="custom-checkbox" type="radio" name="delivery_method" value="home_delivery"> 宅配到府</label>
            `);
        } else if ($('#standard_cash').is(':checked')) {
            $('#cash_on_delivery_options').append(`
                <label><input class="custom-checkbox" type="radio" name="delivery_method" value="convenience_store" checked> 超商取貨</label>
                <label><input class="custom-checkbox" type="radio" name="delivery_method" value="home_delivery"> 宅配到府</label>
            `);
        }
    }

    // 確保每次選擇的配送方式被監聽
    $('input[name="delivery_method"]').off('change').on('change', function() {
        updateSelectedOption(); // 每次變更時更新選項
    });

    // 初始化顯示的選擇
    updateSelectedOption();
}


function updateSelectedOption() {
    const deliveryMethod = $('input[name="delivery_method"]:checked').val();

    // 根據選擇的配送方法和付款方式更新選項
    if (deliveryMethod) {
        if (deliveryMethod === 'convenience_store') {
            if ($('#standard_credit_card').is(':checked')) {
                selectedPaymentOption = '信用卡付款／超商取貨'; // 1
            } else if ($('#standard_cash').is(':checked')) {
                selectedPaymentOption = '貨到付款／超商取貨'; // 3
            }
        } else if (deliveryMethod === 'home_delivery') {
            if ($('#standard_credit_card').is(':checked')) {
                selectedPaymentOption = '信用卡付款／宅配到府'; // 2
            } else if ($('#standard_cash').is(':checked')) {
                selectedPaymentOption = '貨到付款／宅配到府'; // 4
            }
        } else if (deliveryMethod === 'overseas_shipping') {
            selectedPaymentOption = '信用卡付款／宅配到府';
        }
    }

    // 更新 配送和付款方式 顯示選擇的結果
    $('#delivery_options_show h3').text(selectedPaymentOption);
}

$(document).ready(function() {
    // 頁面加載時，隱藏 #delivery_options_show
    $('#delivery_options_show').hide(); // 初始隱藏

    // Step 2 繼續按鈕
    $('#checked_continue_btn_step2').click(function(e) {
        e.preventDefault();

        // 更新選擇的選項
        updateSelectedOption(); // 確保選項根據使用者的選擇更新

        // 確保選擇配送方式
        const deliveryMethod = $('input[name="delivery_method"]:checked').val();

        if (!deliveryMethod) {
            // 如果沒有選擇配送方式，顯示警告訊息
            if ($('.alert-danger').length === 0) {
                $('.options_title:first').after('<div class="alert alert-danger">請選擇配送方式</div>');
            }
            return; // 若未選擇配送方式，阻止繼續執行
        }

        // 移除警告並顯示 #delivery_options_show
        $('.alert-danger').remove();
        console.log('顯示配送選項:', deliveryMethod); // 用來調試
        $('#delivery_options_show').fadeIn(); // 顯示配送選項

        // 繼續處理其他流程
        handleStep2Continue(); // 繼續步驟 2 的流程
    });
});



// 點擊 #prepage_step3 時隱藏 #delivery_options_show
$('#prepage_step3').click(function() {
    $('#delivery_options_show').fadeOut(); // 隱藏選項
});

// 重置監聽
handleDeliveryChange();



function handleStep2Continue() {
    // 獲取被選中的第一層配送方式的 id
    const deliveryMethod = $('input[name="delivery"]:checked').attr('id');

    // 如果沒有選擇配送方式，顯示警告
    if (!deliveryMethod) {
        if ($('.alert-danger').length === 0) {
            $('.options_title:first').after('<div class="alert alert-danger">請選擇配送方式</div>');
        }
        return; // 沒有選擇配送方式時，終止繼續執行
    }

    // 移除警告視窗（如果存在）
    $('.alert-danger').remove();

    $('#delivery_options, #order_info_content_checked, #total_prices_checked, #checked_continue_step2').hide();
    $('#recipient_info_credit_card, #recipient_info_cash_on_delivery, #recipient_info_overseas').hide();

    // 根據第一層配送方式顯示對應的內容
    if (deliveryMethod === 'overseas_shipping') {
        $('#recipient_info_overseas').show(); // 顯示海外配送資訊區塊
        $('.invoice_options').hide();
        $('#total_prices_checked').show();
        $('.order_info').hide();
    } else {
        // 獲取被選中的第二層配送方式的 id
        const deliveryMethod2 = $('input[name="delivery_method"]:checked').val();

        // 如果沒有選擇第二層配送方式，顯示警告
        if (!deliveryMethod2) {
            if ($('.alert-danger').length === 0) {
                $('.options_title:first').after('<div class="alert alert-danger">請選擇配送方式</div>');
            }
            return; // 沒有選擇第二層配送方式時，終止繼續執行
        }

        // 根據選擇的第二層配送方式顯示對應的內容
        if (deliveryMethod2 === 'convenience_store') {
            // 選擇超商取貨（選項1和3）
            $('#recipient_info_credit_card').show(); // 顯示信用卡資訊區塊
            $('.invoice_options').show();
            $('#total_prices_checked').show();
            $('.order_info').hide();
        } else if (deliveryMethod2 === 'home_delivery') {
            // 選擇宅配到府（選項2和4）
            $('#recipient_info_cash_on_delivery').show(); // 顯示貨到付款資訊區塊
            $('.invoice_options').show();
            $('#total_prices_checked').show();
            $('.order_info').hide();
        }
    }

    $('#checked_continue_step3').show();
}





// Step 3 上一頁
function handleStep3Back() {
    $('#recipient_info_credit_card, #recipient_info_cash_on_delivery, #recipient_info_overseas').hide();
    $('#delivery_options, #order_info_content_checked, #total_prices_checked, #checked_continue_step2').show();
    $('#checked_continue_step3').hide();
    $('.invoice_options').hide();
    $('.order_info').show();
    $('#delivery_options_show').hide()
}

$(document).ready(function() {
    $('#prepage_step3').click(function(e) {
        e.preventDefault();
        handleStep3Back();
    });
});



// 自定義下拉式選單
$(document).ready(function() {
    // 顯示選單
    $('.selected-option').click(function() {
        $(this).next('.options-list').toggleClass('active');
    });

    // 點擊選項時更新選中的內容並隱藏選項列表
    $('.options-list div').click(function() {
        let selected = $(this).closest('.custom-select-container').find('.selected-option');
        selected.text($(this).text()); // 更新選中的內容
        $(this).parent().removeClass('active'); // 隱藏選單
    });

    // 點擊外部區域時關閉選單
    $(document).click(function(e) {
        if (!$(e.target).closest('.custom-select-container').length) {
            $('.options-list').removeClass('active');
        }
    });
});


// 修改配送方式
$('input[name="delivery"]').change(function() {
    handleDeliveryChange();
    // 移除警告視窗（如果存在）
    $('.alert-danger').remove();
});



// Step 4
$('#checked_continue_btn_step3').click(function(e) {
    e.preventDefault();
    
    $('#recipient_info_credit_card, #recipient_info_cash_on_delivery, #recipient_info_overseas, .invoice_options, #total_prices_checked, #checked_continue_step3').hide();
    
    $('#checked_finished_page').show();
    $('#order_finished_msg').show();
});




document.addEventListener("DOMContentLoaded", function () {
    const addressMappings = [
        { addressClass: '.mart_address', nextAddressClass: '.mart_address_2' },
        { addressClass: '.the_address', nextAddressClass: '.the_address_2' },
        { addressClass: '.overseas_address', nextAddressClass: '.overseas_address_2' }
    ];

    addressMappings.forEach(mapping => {
        const addressInput = document.querySelector(`${mapping.addressClass} input`);
        const nextAddressInput = document.querySelector(`${mapping.nextAddressClass} input`);

        if (!addressInput || !nextAddressInput) return;

        const textWidthCalculator = document.createElement("span");
        textWidthCalculator.style.position = "absolute";
        textWidthCalculator.style.visibility = "hidden";
        textWidthCalculator.style.whiteSpace = "nowrap";
        document.body.appendChild(textWidthCalculator);

        const updateTextWidthCalculatorStyle = () => {
            const computedStyle = window.getComputedStyle(addressInput);
            textWidthCalculator.style.font = computedStyle.font;
            textWidthCalculator.style.fontSize = computedStyle.fontSize;
            textWidthCalculator.style.fontFamily = computedStyle.fontFamily;
        };
        updateTextWidthCalculatorStyle();

        const calculateTextWidth = (text) => {
            textWidthCalculator.textContent = text;
            return textWidthCalculator.offsetWidth;
        };

        let isComposing = false; // 用來追蹤是否正在組字

        const checkWidthAndMoveFocus = () => {
            const isSmallScreen = window.innerWidth <= 430;
            if (isSmallScreen && !isComposing) { // 僅在組字完成後檢查
                const textWidth = calculateTextWidth(addressInput.value);
                if (textWidth > 170) {
                    const currentValue = addressInput.value;
                    let visibleText = "";
                    let overflowText = "";

                    // 按字元計算直到寬度超過 170px
                    for (let i = 0; i < currentValue.length; i++) {
                        const testText = visibleText + currentValue[i];
                        if (calculateTextWidth(testText) <= 170) {
                            visibleText += currentValue[i];
                        } else {
                            overflowText = currentValue.slice(i);
                            break;
                        }
                    }

                    // 更新輸入框
                    addressInput.value = visibleText;
                    nextAddressInput.value = overflowText;
                    nextAddressInput.focus();
                }
            }
        };

        // 處理組字事件
        addressInput.addEventListener("compositionstart", () => {
            isComposing = true;
        });

        addressInput.addEventListener("compositionupdate", () => {
            // 可選：即時更新 UI 或其他邏輯
        });

        addressInput.addEventListener("compositionend", () => {
            isComposing = false;
            checkWidthAndMoveFocus(); // 組字完成後重新檢查
        });

        // 處理貼上文字
        addressInput.addEventListener("paste", (event) => {
            const isSmallScreen = window.innerWidth <= 430;
            if (isSmallScreen) {
                event.preventDefault();
                const pastedText = event.clipboardData.getData("text");
                const currentValue = addressInput.value;

                let combinedText = currentValue + pastedText;
                let visibleText = "";
                let overflowText = "";

                // 按字元計算直到寬度超過 170px
                for (let i = 0; i < combinedText.length; i++) {
                    const testText = visibleText + combinedText[i];
                    if (calculateTextWidth(testText) <= 170) {
                        visibleText += combinedText[i];
                    } else {
                        overflowText = combinedText.slice(i);
                        break;
                    }
                }

                // 更新輸入框
                addressInput.value = visibleText;
                if (overflowText) {
                    nextAddressInput.value = overflowText;
                    nextAddressInput.focus();
                }
            }
        });

        // 監聽輸入事件
        addressInput.addEventListener("input", checkWidthAndMoveFocus);
    });
});


