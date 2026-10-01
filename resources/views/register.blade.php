<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>تسجيل مستخدم جديد | OptiFace</title>
    
    <script src="/face-api.min.js"></script>
    
    <style>
        :root { --primary-green: #10A04A; --light-green: #E8F6EE; }
        body { background-color: #FFFFFF; color: #333333; font-family: 'Segoe UI', Tahoma, sans-serif; display: flex; flex-direction: column; align-items: center; padding: 15px; margin: 0; }
        .logo-container img { max-height: 80px; margin-bottom: 10px; }
        
        .form-box { width: 100%; max-width: 500px; background: #FFFFFF; padding: 20px; border-radius: 15px; border: 2px solid var(--primary-green); box-shadow: 0 4px 15px rgba(16, 160, 74, 0.15); margin-top: 10px; box-sizing: border-box; }
        
        .input-group { margin-bottom: 15px; position: relative; }
        .input-group label { display: block; font-weight: bold; margin-bottom: 5px; color: var(--primary-green); font-size: 0.9em; }
        .input-group input[type="text"], .input-group input[type="email"], .input-group input[type="password"], .input-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; font-size: 1em; box-sizing: border-box; font-family: inherit; transition: 0.3s; }
        .input-group input:focus, .input-group select:focus { border-color: var(--primary-green); outline: none; box-shadow: 0 0 5px rgba(16, 160, 74, 0.3); }
        
        .password-tracker { display: none; background: #f9f9f9; border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px 15px; margin-top: 5px; font-size: 0.85em; transition: 0.3s; }
        .password-tracker ul { list-style: none; padding: 0; margin: 0; }
        .password-tracker li { margin-bottom: 5px; color: #dc3545; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .password-tracker li::before { content: '❌'; font-size: 0.9em; }
        .password-tracker li.valid { color: var(--primary-green); }
        .password-tracker li.valid::before { content: '✅'; }

        .show-password-container { display: flex; align-items: center; margin-bottom: 15px; font-size: 0.9em; color: var(--primary-green); }
        .show-password-container input { margin-left: 8px; cursor: pointer; }
        .show-password-container label { cursor: pointer; user-select: none; font-weight: bold; }

        .upload-options { display: flex; gap: 10px; margin-bottom: 15px; justify-content: center; }
        .opt-btn { flex: 1; padding: 12px; border: none; border-radius: 8px; background: var(--primary-green); color: #fff; font-weight: bold; cursor: pointer; transition: 0.3s; font-family: inherit; font-size: 1em;}
        .opt-btn:active { transform: scale(0.95); background: #0d823b; }

        .image-upload-box { text-align: center; margin-bottom: 15px; padding: 15px; border: 2px dashed var(--primary-green); border-radius: 10px; background-color: var(--light-green); transition: 0.3s; }
        .image-upload-box.locked { opacity: 0.5; pointer-events: none; cursor: not-allowed; } 
        
        .camera-container { display: none; flex-direction: column; align-items: center; gap: 10px; width: 100%; margin-top: 15px; }
        #webcam { width: 100%; max-width: 320px; border-radius: 10px; border: 2px solid var(--primary-green); background: #000; transform: scaleX(-1); } 
        
        .btn-capture { background: #333; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 1em; width: 100%; max-width: 320px;}
        .btn-capture:active { transform: scale(0.95); }

        #recapture-btn { display: none; background: #6c757d; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; margin: 10px auto 0 auto; cursor: pointer; transition: 0.3s; }
        #recapture-btn:active { transform: scale(0.95); }

        #image-preview { width: 100%; max-width: 250px; height: auto; border-radius: 10px; margin-top: 15px; display: none; margin-left: auto; margin-right: auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        .btn-submit { width: 100%; background-color: var(--primary-green); color: white; border: none; padding: 14px; font-size: 1.1em; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.3s; display: none; margin-top: 10px; }
        .btn-submit.active { display: block; animation: fadeIn 0.5s; }
        .btn-submit.active:active { background-color: #0d823b; transform: scale(0.98); }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="logo-container">
        <img src="/logo.png" alt="الشعار">
    </div>

    <h3 id="status-text" style="color: var(--primary-green); margin-top: 0; text-align: center; font-size: 1.1em;">جاري تحميل النظام... ⏳</h3>

    <div class="form-box">
    <form action="/register" method="POST" id="register-form" enctype="multipart/form-data">
            @csrf
            
            <div class="input-group">
                <label>الاسم الرباعي</label>
                <input type="text" name="name" required placeholder="مثال: أحمد محمد علي">
            </div>
            <div class="input-group">
                <label>الرقم الجامعي</label>
                <input type="text" name="university_id" required placeholder="مثال: 202310452">
            </div>
            <div class="input-group">
                <label>البريد الإلكتروني (اسم المستخدم)</label>
                <input type="email" name="email" required placeholder="example@student.com" dir="ltr">
            </div>
            
            <div class="input-group">
                <label>كلمة المرور</label>
                <input type="password" id="password" name="password" required placeholder="******" dir="ltr" minlength="8" title="يرجى استيفاء جميع شروط كلمة المرور">
                
                <div class="password-tracker" id="password-tracker">
                    <ul>
                        <li id="req-length">8 خانات على الأقل</li>
                        <li id="req-capital">يحتوي على حرف كبير (Capital)</li>
                        <li id="req-symbol">يحتوي على رمز أو علامة ترقيم (@, #, !)</li>
                    </ul>
                </div>
            </div>
            
            <div class="input-group" style="margin-bottom: 10px;">
                <label>تأكيد كلمة المرور</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="******" dir="ltr" minlength="8">
                
                <div class="password-tracker" id="match-tracker">
                    <ul>
                        <li id="req-match">كلمتا المرور متطابقتان</li>
                    </ul>
                </div>
            </div>
            
            <div class="show-password-container">
                <input type="checkbox" id="show-passwords" onclick="togglePasswords()">
                <label for="show-passwords">إظهار كلمة المرور</label>
            </div>

            <!-- 🚀 التعديل هنا: جلب الكليات من قاعدة البيانات -->
            <div class="input-group">
                <label>الكلية</label>
                <select name="faculty" id="faculty" required onchange="updateDepartments()">
                    <option value="" disabled selected>اختر الكلية...</option>
                    @foreach($faculties as $fac)
                        <option value="{{ $fac->name }}" data-depts="{{ $fac->departments }}">{{ $fac->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label>القسم</label>
                <select name="department" id="department" required>
                    <option value="" disabled selected>اختر الكلية أولاً...</option>
                </select>
            </div>

            <div class="image-upload-box" id="upload-box-container">
                <label style="color:var(--primary-green); font-weight:bold; display: block; margin-bottom: 15px;">📸 طريقة إرفاق الصورة الشخصية</label>
                
                <div class="upload-options">
                    <button type="button" class="opt-btn" onclick="triggerFileInput()">📁 رفع من الجهاز</button>
                    <button type="button" class="opt-btn" onclick="triggerCamera()">📷 التقاط بالكاميرا</button>
                </div>

                <input type="file" id="image-upload" name="personal_photo" accept="image/*" style="display: none;">

                <div id="camera-area" class="camera-container">
                    <video id="webcam" autoplay muted playsinline></video>
                    <button type="button" class="btn-capture" id="capture-btn" onclick="capturePhoto()">التقط الصورة الآن 📸</button>
                </div>

                <img id="image-preview" alt="معاينة الصورة">
                
                <button type="button" id="recapture-btn" onclick="triggerCamera()">إعادة التقاط 🔄</button>
            </div>

            <input type="hidden" name="face_encoding" id="face_encoding">

            <button type="submit" class="btn-submit" id="submit-btn">إنشاء حساب وحفظ البصمة ✅</button>
        </form>
    </div>

    <script>
        // 🚀 التعديل هنا: دالة تحديث الأقسام بتقرأ من الـ data-depts اللي جاي من الداتا بيز
        function updateDepartments() {
            const facultySelect = document.getElementById('faculty');
            const deptSelect = document.getElementById('department');
            const selectedOption = facultySelect.options[facultySelect.selectedIndex];
            const depts = selectedOption.getAttribute('data-depts');
            
            deptSelect.innerHTML = '<option value="" disabled selected>اختر القسم...</option>';
            
            if (depts) {
                let deptArray = depts.replace(/,/g, '،').split('،');
                deptArray.forEach(dept => {
                    if (dept.trim()) {
                        const option = document.createElement('option');
                        option.value = dept.trim();
                        option.textContent = dept.trim();
                        deptSelect.appendChild(option);
                    }
                });
            }
        }

        const statusText = document.getElementById('status-text');
        const imageUpload = document.getElementById('image-upload');
        const imagePreview = document.getElementById('image-preview');
        const submitBtn = document.getElementById('submit-btn');
        const faceEncodingInput = document.getElementById('face_encoding');
        const registerForm = document.getElementById('register-form');
        const uploadBoxContainer = document.getElementById('upload-box-container');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const recaptureBtn = document.getElementById('recapture-btn');

        const passwordTracker = document.getElementById('password-tracker');
        const matchTracker = document.getElementById('match-tracker');
        const reqLength = document.getElementById('req-length');
        const reqCapital = document.getElementById('req-capital');
        const reqSymbol = document.getElementById('req-symbol'); 
        const reqMatch = document.getElementById('req-match');

        let isProcessingImage = false;
        let isSubmitting = false;
        let currentMode = 'file'; 
        let cameraStream = null;
        let capturedBlob = null; 

        function togglePasswords() {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                confirmPasswordInput.type = "text";
            } else {
                passwordInput.type = "password";
                confirmPasswordInput.type = "password";
            }
        }

        passwordInput.addEventListener('focus', () => {
            passwordTracker.style.display = 'block';
            if(passwordInput.value === '') passwordInput.setCustomValidity('يرجى استيفاء جميع شروط كلمة المرور');
        });

        passwordInput.addEventListener('input', () => {
            const val = passwordInput.value;
            
            let isLengthValid = val.length >= 8;
            let isCapitalValid = /[A-Z]/.test(val);
            let isSymbolValid = /[^a-zA-Z0-9]/.test(val); 

            if(isLengthValid) reqLength.classList.add('valid');
            else reqLength.classList.remove('valid');

            if(isCapitalValid) reqCapital.classList.add('valid');
            else reqCapital.classList.remove('valid');

            if(isSymbolValid) reqSymbol.classList.add('valid');
            else reqSymbol.classList.remove('valid');

            if (isLengthValid && isCapitalValid && isSymbolValid) {
                passwordInput.setCustomValidity('');
            } else {
                passwordInput.setCustomValidity('يرجى استيفاء جميع شروط كلمة المرور');
            }

            validatePasswordMatch();
        });

        confirmPasswordInput.addEventListener('focus', () => {
            matchTracker.style.display = 'block';
        });

        function validatePasswordMatch() {
            if (confirmPasswordInput.value === '') {
                reqMatch.classList.remove('valid');
                confirmPasswordInput.setCustomValidity('يرجى التأكيد');
            } else if (passwordInput.value === confirmPasswordInput.value) {
                reqMatch.classList.add('valid');
                confirmPasswordInput.setCustomValidity('');
            } else {
                reqMatch.classList.remove('valid');
                confirmPasswordInput.setCustomValidity('كلمتا المرور غير متطابقتين!');
            }
        }
        
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);

        function triggerFileInput() {
            if (isProcessingImage) return;
            currentMode = 'file';
            stopWebcam();
            document.getElementById('camera-area').style.display = 'none';
            imageUpload.click(); 
        }

        async function triggerCamera() {
            if (isProcessingImage) return;
            currentMode = 'camera';
            
            imageUpload.value = '';
            capturedBlob = null;
            imagePreview.style.display = 'none';
            imagePreview.src = '';
            faceEncodingInput.value = '';
            submitBtn.classList.remove('active'); 
            recaptureBtn.style.display = 'none';
            
            document.getElementById('camera-area').style.display = 'flex';
            await startWebcam();
        }

        async function startWebcam() {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: 640, height: 480, facingMode: "user" } 
                });
                document.getElementById('webcam').srcObject = cameraStream;
                statusText.innerText = "الكاميرا تعمل، وجه وجهك واضغط التقاط 📸";
                statusText.style.color = "var(--primary-green)";
            } catch (err) {
                console.error("خطأ في تشغيل الكاميرا:", err);
                statusText.innerText = "❌ فشل الوصول للكاميرا، يرجى التحقق من الصلاحيات.";
                statusText.style.color = "red";
            }
        }

        function stopWebcam() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        }

        function capturePhoto() {
            const webcam = document.getElementById('webcam');
            if (!webcam.srcObject) return;

            const canvas = document.createElement('canvas');
            canvas.width = webcam.videoWidth || 640;
            canvas.height = webcam.videoHeight || 480;
            
            const ctx = canvas.getContext('2d');
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(webcam, 0, 0, canvas.width, canvas.height);
            
            const dataUrl = canvas.toDataURL('image/jpeg');
            imagePreview.src = dataUrl;
            imagePreview.style.display = 'block';
            
            stopWebcam();
            document.getElementById('camera-area').style.display = 'none';

            canvas.toBlob(async (blob) => {
                capturedBlob = blob;
                await processImage(blob); 
            }, 'image/jpeg');
        }

        async function processImage(imageBlobOrFile) {
            try {
                isProcessingImage = true;
                uploadBoxContainer.classList.add('locked');

                statusText.innerText = "جاري تهيئة الصورة واستخراج البصمة... ⏳";
                statusText.style.color = "var(--primary-green)";
                submitBtn.classList.remove('active'); 

                const imgElement = await faceapi.bufferToImage(imageBlobOrFile);
                
                const detection = await faceapi.detectSingleFace(imgElement, new faceapi.TinyFaceDetectorOptions({ inputSize: 416, scoreThreshold: 0.1 }))
                                               .withFaceLandmarks()
                                               .withFaceDescriptor();

                if (detection) {
                    const encodingStr = JSON.stringify(Array.from(detection.descriptor));
                    
                    statusText.innerText = "جاري التحقق من الهوية في قاعدة البيانات... 🔍";
                    
                    const csrfToken = document.querySelector('input[name="_token"]').value;
                    const checkResponse = await fetch('/check-face', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ face_encoding: encodingStr })
                    });
                    
                    const result = await checkResponse.json();
                    
                    if (result.is_duplicate) {
                        statusText.innerHTML = `❌ عذراً! هذا الوجه مسجل بالفعل باسم: <strong style="color:blue;">${result.name}</strong>`;
                        statusText.style.color = "red";
                        resetInput();
                    } else {
                        faceEncodingInput.value = encodingStr;
                        statusText.innerText = "تم استخراج البصمة! يمكنك إنشاء الحساب الآن ✅";
                        
                        submitBtn.classList.add('active'); 
                        submitBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        if (currentMode === 'camera') {
                            recaptureBtn.style.display = 'inline-block';
                        }
                    }
                } else {
                    statusText.innerText = "❌ لم يتم التعرف على وجه، يرجى توجيه وجهك بشكل صحيح وإضاءة جيدة.";
                    statusText.style.color = "red";
                    resetInput();
                }
            } catch (error) {
                console.error(error);
                statusText.innerText = "❌ حدث خطأ في معالجة البصمة، جرب محاولة أخرى.";
                statusText.style.color = "red";
                resetInput();
            } finally {
                isProcessingImage = false;
                uploadBoxContainer.classList.remove('locked');
            }
        }

        function resetInput() {
            imageUpload.value = ""; 
            imagePreview.style.display = 'none';
            capturedBlob = null;
            recaptureBtn.style.display = 'none';
            submitBtn.classList.remove('active'); 
            if (currentMode === 'camera') {
                document.getElementById('camera-area').style.display = 'flex';
                startWebcam();
            }
        }

        imageUpload.addEventListener('change', async (event) => {
            if (isProcessingImage) return;
            const file = event.target.files[0];
            if (!file) return;

            imagePreview.src = URL.createObjectURL(file);
            imagePreview.style.display = 'block';
            recaptureBtn.style.display = 'none';

            await processImage(file);
        });

        registerForm.addEventListener('submit', async function(event) {
            event.preventDefault(); 

            // التحقق من شروط كلمة المرور
            const val = passwordInput.value;
            if (!(val.length >= 8 && /[A-Z]/.test(val) && /[^a-zA-Z0-9]/.test(val))) {
                passwordInput.reportValidity();
                return;
            }

            // التحقق من وجود الصورة
            if (currentMode === 'file' && !imageUpload.files[0]) {
                statusText.innerText = "❌ يرجى اختيار صورة شخصية من الجهاز!";
                statusText.style.color = "red";
                return;
            }

            if (currentMode === 'camera' && !capturedBlob) {
                statusText.innerText = "❌ يرجى التقاط صورة بالكاميرا أولاً!";
                statusText.style.color = "red";
                return;
            }

            // التحقق من وجود بصمة الوجه
            if (!faceEncodingInput.value) {
                statusText.innerText = "❌ يرجى الانتظار حتى يتم استخراج بصمة الوجه!";
                statusText.style.color = "red";
                return;
            }

            if (isSubmitting) return;
            isSubmitting = true;

            submitBtn.innerText = "جاري إرسال البيانات... ⏳";
            
            try {
                const formData = new FormData(this);
                
                if (currentMode === 'camera' && capturedBlob) {
                    formData.delete('personal_photo');
                    formData.append('personal_photo', capturedBlob, 'captured_face.jpg');
                }

                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json' // مهم جداً عشان لارافيل يرد بـ JSON
                    }
                });

                if (response.ok) {
                    // نجاح التسجيل
                    window.location.href = '/login'; 
                } else if (response.status === 422) {
                    // 🚀 التعديل هنا: قراءة أخطاء لارافيل (Validation Errors) وعرضها
                    const errorData = await response.json();
                    let errorMessages = "❌ حدثت الأخطاء التالية:<br>";
                    
                    // استخراج جميع رسائل الخطأ من الاستجابة
                    for (const [key, messages] of Object.entries(errorData.errors)) {
                        errorMessages += `- ${messages[0]}<br>`;
                    }
                    
                    statusText.innerHTML = errorMessages;
                    statusText.style.color = "red";
                    submitBtn.innerText = "إنشاء حساب وحفظ البصمة ✅";
                    isSubmitting = false; 

                } else {
                    // أخطاء السيرفر الأخرى (500 وغيرها)
                    statusText.innerText = "❌ حدث خطأ داخلي في السيرفر."; 
                    statusText.style.color = "red";
                    submitBtn.innerText = "إنشاء حساب وحفظ البصمة ✅";
                    isSubmitting = false; 
                }
            } catch (error) {
                statusText.innerText = "❌ حدث خطأ في الاتصال بالخادم.";
                statusText.style.color = "red";
                submitBtn.innerText = "إنشاء حساب وحفظ البصمة ✅";
                isSubmitting = false; 
            }
        });
        async function initSystem() {
            try {
                await faceapi.tf.setBackend('cpu');
                await faceapi.tf.ready();
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri("/weights"),
                    faceapi.nets.faceLandmark68Net.loadFromUri("/weights"),
                    faceapi.nets.faceRecognitionNet.loadFromUri("/weights")
                ]);
                statusText.innerText = "النظام جاهز، اختر طريقة التسجيل وأدخل بياناتك 📝";
            } catch (error) {
                statusText.innerText = "❌ حدث خطأ في تحميل النظام.";
                statusText.style.color = "red";
            }
        }

        initSystem();
    </script>
</body>
</html>