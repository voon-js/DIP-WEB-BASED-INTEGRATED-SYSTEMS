document.addEventListener('DOMContentLoaded', function () {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('profile_pic');
    const previewImage = document.getElementById('previewImage');
    const fileNameSpan = document.getElementById('file-name');


    const customFileUpload = document.querySelector('.custom-file-upload');
    
    // 阻止ChooseFile弄到dropZone
    customFileUpload.addEventListener('click', function(e) {
        e.stopPropagation(); 
    });


    // 拖放区域
    dropZone.addEventListener('click', () => fileInput.click());

    // 拖放事
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');

        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            updateFilePreview(fileInput.files[0]);
        }
    });

    // 文件选择
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            updateFilePreview(fileInput.files[0]);
        }
    });

    // 更新预览和文件名
    function updateFilePreview(file) {
        fileNameSpan.textContent = file.name;

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    // 电话号码格式化验证功能
    const contactInput = document.getElementById('contact_no');
    if (contactInput) {
        //显示时添加+60前缀
        const dbNumber = contactInput.value; // 数据库存储的是纯数字（如"123456789"）
        contactInput.value = '+60' + dbNumber; // 显示为"+60123456789"

        // 保留+60前缀
        contactInput.addEventListener('input', function(e) {
            // 如果用户尝试删除 +60 前缀，直接阻止
            if (!this.value.startsWith('+60')) {
                this.value = '+60' + this.value.slice(3); // 强制保留 +60
                // 移动光标到末尾
                this.setSelectionRange(this.value.length, this.value.length);
            }
        });
        // 不提交+60，只提交后面
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submittedNumber = contactInput.value.replace(/^\+60/, '');
                // 验证是否为9-14位数字
                if (!/^[0-9]{9,14}$/.test(submittedNumber)) {
                    e.preventDefault();
                    alert('Contact number must be 9-14 digits (after +60)');
                    return;
                }
                contactInput.value = submittedNumber;
            });
        }
    }
});