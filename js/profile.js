
function openHistory() {
 
    document.querySelectorAll('.forms').forEach(form => {
      form.classList.remove('form-hide');
      form.style.display = 'block';
    });
    
    // 显示订单历史
    document.querySelector('.order-history').closest('.forms').style.display = 'block';
    
    // 隐藏个人资料
    document.getElementById('personal_details').style.display = 'none';
    
    // 隐藏密码修改表单
    document.querySelector('.password-change-form').closest('.forms').style.display = 'none';
  }
  
  function personalDetails() {
    // 显示个人资料
    document.getElementById('personal_details').style.display = 'block';
    
    // 隐藏其他内容
    document.querySelectorAll('.forms').forEach(form => {
      form.style.display = 'none';
    });
  }
  
  function changePassword() {
    // 显示修改密码表单
    document.querySelector('.password-change-form').closest('.forms').style.display = 'block';
    
    // 隐藏其他内容
    document.getElementById('personal_details').style.display = 'none';
    document.querySelector('.order-history').closest('.forms').style.display = 'none';
  }