<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 🚀 السطر ده هو الحل الجذري لمشكلة 419 -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>نظام الحضور الذكي</title>
    
    <script src="/face-api.min.js"></script>
    
    <style>
        :root { --primary-green: #10A04A; --light-green: #E8F6EE; --danger-red: #dc3545; }
        body { background-color: #F8FBFA; color: #333333; font-family: 'Segoe UI', Tahoma, sans-serif; display: flex; flex-direction: column; align-items: center; padding: 20px; margin: 0; }
        .logo-container { margin-bottom: 15px; }
        .logo-container img { max-height: 120px; }

        .camera-box { background: #000; padding: 10px; border-radius: 20px; box-shadow: 0 10px 30px rgba(16, 160, 74, 0.1); margin-bottom: 20px; position: relative; display: flex; justify-content: center; align-items: center; overflow: hidden; width: 100%; max-width: 720px; }
        video { border-radius: 12px; display: block; width: 100%; height: auto; max-width: 720px; }
        canvas { position: absolute; }

        .guidance-box { position: absolute; top: 25px; left: 50%; transform: translateX(-50%); background: rgba(255, 255, 255, 0.95); padding: 10px 30px; border-radius: 30px; border: 2px solid var(--primary-green); font-weight: bold; font-size: 1.1em; color: var(--primary-green); z-index: 10; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; width: 85%; max-width: 320px; }
        
        .table-box { width: 100%; max-width: 720px; background: #FFFFFF; padding: 20px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); margin-bottom: 20px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: center; }
        th, td { padding: 15px; border-bottom: 1px solid #F0F0F0; }
        th { color: #666; font-weight: bold; font-size: 1em; }

        .btn-logout { color: var(--danger-red); text-decoration: none; font-weight: bold; border: 2px solid var(--danger-red); padding: 10px 25px; border-radius: 12px; transition: 0.3s; margin-bottom: 20px; display: inline-block;}
        .btn-logout:hover { background: var(--danger-red); color: white; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2); }

        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            display: none; justify-content: center; align-items: center; z-index: 2000; opacity: 0; transition: opacity 0.3s ease;
        }
        
        .modal-overlay.show { display: flex; opacity: 1; }
        .modal-content { background: #ffffff; padding: 40px; border-radius: 24px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); width: 90%; max-width: 400px; transform: scale(0.9) translateY(20px); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .modal-overlay.show .modal-content { transform: scale(1) translateY(0); }

        .success-animation { margin: 0 auto 20px auto; display: flex; justify-content: center; }
        .checkmark { width: 80px; height: 80px; border-radius: 50%; display: block; stroke-width: 4; stroke: var(--primary-green); stroke-miterlimit: 10; animation: scale .3s ease-in-out .9s both; }
        .checkmark__circle { stroke-dasharray: 166; stroke-dashoffset: 166; stroke-width: 4; stroke-miterlimit: 10; stroke: var(--primary-green); fill: rgba(16, 160, 74, 0.1); animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards; }
        .checkmark__check { transform-origin: 50% 50%; stroke-dasharray: 48; stroke-dashoffset: 48; animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards; }
        
        .warning-icon { font-size: 5em; color: var(--danger-red); margin-bottom: 10px; animation: scale 0.5s ease-in-out; }

        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
        @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }

        .modal-content h3 { color: #1a1a1a; margin: 0 0 10px 0; font-size: 1.8em; }
        .modal-content p { color: #666; font-size: 1.1em; margin-bottom: 30px; font-weight: 500; }
        
        .btn-modal { background: var(--primary-green); color: white; border: none; padding: 14px 0; width: 100%; font-size: 1.1em; font-weight: bold; border-radius: 14px; cursor: pointer; transition: all 0.3s ease; }
        .btn-modal:disabled { opacity: 0.7; cursor: not-allowed; }
        .btn-modal-danger { background: var(--danger-red); color: white; border: none; padding: 14px 0; width: 100%; font-size: 1.1em; font-weight: bold; border-radius: 14px; cursor: pointer; transition: all 0.3s ease; }
    </style>
</head>
<body>

    <div class="logo-container">
        <img src="/logo.png" alt="شعار الجامعة">
    </div>

    <h2 id="status-text" style="color: var(--primary-green); margin-top: 0; margin-bottom: 20px;">جاري تحميل النظام... ⏳</h2>

    <div class="camera-box">
        <div id="guidance-box" class="guidance-box">جاري تشغيل الكاميرا...</div>
        <video id="video" autoplay muted playsinline></video>
    </div>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>اسم الطالب</th>
                    <th>الكلية (القسم)</th>
                    <th>الحالة</th>
                    <th>وقت الحضور (توقيت السودان)</th>
                </tr>
            </thead>
            <tbody id="attendance-list">
                <tr>
                    <td colspan="4" style="color: gray;">في انتظار التعرف على الوجوه...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <a href="/login" class="btn-logout">العودة للشاشة الرئيسية </a>

    <div id="success-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="success-animation">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>
            <h3 id="welcome-title">مرحباً بك!</h3>
            <p>تم التعرف عليك بنجاح. يرجى التأكيد لتسجيل الحضور والانتقال للوحة التحكم.</p>
            <button id="confirm-btn" class="btn-modal" onclick="confirmAndSave()">استمرار وتأكيد الدخول</button>
        </div>
    </div>

    <div id="warning-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="warning-icon">⚠️</div>
            <h3 style="color: var(--danger-red);">عذراً!</h3>
            <p style="color: #444; line-height: 1.6;">لم نتمكن من التعرف عليك.<br>الرجاء تسجيل كطالب جديد.</p>
            <button class="btn-modal-danger" onclick="returnToLogin()">العودة لشاشة الدخول</button>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const video = document.getElementById('video');
        const statusText = document.getElementById('status-text');
        const guidanceBox = document.getElementById('guidance-box');
        const successModal = document.getElementById('success-modal');
        const warningModal = document.getElementById('warning-modal');
        const confirmBtn = document.getElementById('confirm-btn');
        
        let faceMatcher = null;
        let isRecorded = false; 
        let isProcessing = false; 
        let isBlocked = false; 
        let failedAttempts = 0; 
        
        let currentStudentId = null;
        let currentStudentName = '';
        let currentStudentFaculty = '';
        
        const studentsData = {!! isset($students) ? $students->toJson() : '[]' !!};
        const MODEL_URL = '/weights';

        Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]).then(() => {
            const labeledDescriptors = [];
            studentsData.forEach(student => {
                try {
                    const encodingArray = JSON.parse(student.face_encoding);
                    if (encodingArray && encodingArray.length === 128) {
                        const float32Array = new Float32Array(encodingArray);
                        labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(student.id.toString(), [float32Array]));
                    }
                } catch(e) {}
            });

            if (labeledDescriptors.length > 0) {
                faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.45);
            }

            statusText.innerText = "النظام جاهز للعمل ✅";
            startVideo();
        }).catch(err => {
            console.error("خطأ في تحميل النماذج:", err);
            statusText.innerText = "❌ حدث خطأ في تحميل ملفات النظام!";
            statusText.style.color = "red";
        });

        function startVideo() {
            statusText.innerText = "نظام الحضور بالذكاء الاصطناعي جاهز 📷";
            guidanceBox.innerText = "جاري طلب تشغيل الكاميرا...";
            
            navigator.mediaDevices.getUserMedia({ 
                video: { width: { ideal: 640 }, height: { ideal: 480 }, frameRate: { ideal: 15 } }, 
                audio: false 
            })
            .then(stream => { 
                video.srcObject = stream; 
                guidanceBox.innerText = "يرجى توجيه الوجه للكاميرا";
            })
            .catch(err => { 
                statusText.innerText = "❌ فشل تشغيل الكاميرا!";
            });
        }

        async function getSudanTime() {
            try {
                const response = await fetch('https://timeapi.io/api/Time/current/zone?timeZone=Africa/Khartoum');
                if (response.ok) {
                    const data = await response.json();
                    const date = new Date(data.dateTime);
                    return date.toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                }
                throw new Error('API Timeout');
            } catch (error) {
                return new Date().toLocaleTimeString('ar-EG', {
                    timeZone: 'Africa/Khartoum',
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });
            }
        }

        function showModal() { successModal.classList.add('show'); }
        function closeSuccessModal() { 
            successModal.classList.remove('show'); 
            setTimeout(() => { successModal.style.display = 'none'; }, 300);
        }

        function showWarningModal() { 
            warningModal.style.display = 'flex';
            setTimeout(() => { warningModal.classList.add('show'); }, 10);
        }
        
        function returnToLogin() {
            window.location.href = "/login";
        }

        // 🚀 الدالة السليمة تماماً للتأكيد والتوجيه
        function confirmAndSave() {
            if (!currentStudentId) return;
            
            confirmBtn.innerText = "جاري التأكيد وتسجيل الدخول... ⏳";
            confirmBtn.disabled = true;

            fetch('/face-login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ user_id: currentStudentId })
            }).then(res => {
                return res.json();
            }).then(loginData => {
                if (loginData.success) {
                    return fetch('/save-student-attendance', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ user_id: currentStudentId, is_complete: true })
                    });
                } else {
                    if (loginData.message === 'account_frozen') throw new Error('frozen');
                    throw new Error('login_failed');
                }
            }).then(response => {
                if (!response.ok) throw new Error('server_error');
                return response.json();
            }).then(data => {
                confirmBtn.innerText = "تم بنجاح! جاري تحويلك لنظام النتائج... 🚀";
                confirmBtn.style.backgroundColor = "#0e8c41"; 
                
                setTimeout(() => {
                    window.location.href = "{{ env('RESULTS_APP_URL') }}/student?id=" + currentStudentId;
                }, 1000);
            }).catch(error => {
                console.error("Error:", error);
                if (error.message === 'frozen') {
                    confirmBtn.innerText = "❌ حسابك مجمد، راجع الإدارة";
                } else {
                    confirmBtn.innerText = "❌ حدث خطأ، حاول مجدداً";
                }
                confirmBtn.style.backgroundColor = "var(--danger-red)";
                confirmBtn.disabled = false;
            });
        }

        // 🚀 دالة الذكاء الاصطناعي التي تم استرجاعها
        const detectFaces = async () => {
            if (isRecorded || isProcessing || isBlocked) return;

            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.3, inputSize: 224 }))
                                            .withFaceLandmarks()
                                            .withFaceDescriptors();

            const canvas = document.querySelector('canvas');
            const displaySize = { width: video.videoWidth, height: video.videoHeight };
            if(canvas) {
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                faceapi.draw.drawDetections(canvas, resizedDetections);
            }

            if (detections.length > 0 && faceMatcher) {
                isProcessing = true;
                guidanceBox.innerText = "جاري التحقق بدقة عالية...";

                const bestMatch = faceMatcher.findBestMatch(detections[0].descriptor);

                if (bestMatch.label !== 'unknown') { 
                    isRecorded = true;
                    failedAttempts = 0; 
                    
                    const matchedStudent = studentsData.find(s => s.id.toString() === bestMatch.label);
                    currentStudentName = matchedStudent ? matchedStudent.name : 'طالب';
                    
                    const faculty = matchedStudent && matchedStudent.faculty ? matchedStudent.faculty : '—';
                    const department = matchedStudent && matchedStudent.department ? ' (' + matchedStudent.department + ')' : '';
                    currentStudentFaculty = faculty + department;
                    
                    currentStudentId = bestMatch.label;

                    guidanceBox.innerText = "✅ تم التأكيد بنجاح!";
                    guidanceBox.style.color = "var(--primary-green)";
                    guidanceBox.style.borderColor = "var(--primary-green)";
                    
                    document.getElementById('welcome-title').innerText = "مرحباً بك، " + currentStudentName + "!";
                    
                    successModal.style.display = 'flex';
                    setTimeout(() => showModal(), 10);

                } else {
                    failedAttempts++;
                    
                    if (failedAttempts >= 3) {
                        isBlocked = true;
                        guidanceBox.innerText = "❌ فشل التحقق!";
                        guidanceBox.style.color = "white";
                        guidanceBox.style.backgroundColor = "var(--danger-red)";
                        guidanceBox.style.borderColor = "var(--danger-red)";
                        
                        showWarningModal();
                        
                        setTimeout(() => { returnToLogin(); }, 5000);
                    } else {
                        guidanceBox.style.color = "red";
                        guidanceBox.style.borderColor = "red";
                        
                        let nextAttempt = failedAttempts + 1;
                        for (let c = 3; c > 0; c--) {
                            guidanceBox.innerText = `لم نتعرف عليك! المحاولة ${nextAttempt} ستبدأ بعد ${c}...`;
                            await new Promise(r => setTimeout(r, 1000));
                        }
                        
                        isProcessing = false;
                    }
                }
            } else {
                guidanceBox.innerText = "يرجى توجيه الوجه للكاميرا في إضاءة جيدة";
                guidanceBox.style.color = "var(--primary-green)";
                guidanceBox.style.borderColor = "var(--primary-green)";
                guidanceBox.style.backgroundColor = "rgba(255, 255, 255, 0.95)";
            }
            
            if(!isRecorded && !isBlocked) {
                setTimeout(detectFaces, 500); 
            }
        };

        video.addEventListener('playing', () => {
            const canvas = faceapi.createCanvasFromMedia(video);
            document.querySelector('.camera-box').append(canvas);
            
            const displaySize = { width: video.videoWidth, height: video.videoHeight };
            faceapi.matchDimensions(canvas, displaySize);

            canvas.style.width = video.offsetWidth + "px";
            canvas.style.height = video.offsetHeight + "px";
            canvas.style.top = video.offsetTop + "px";
            canvas.style.left = video.offsetLeft + "px";

            detectFaces();
        });

        window.addEventListener('resize', () => {
            const canvas = document.querySelector('canvas');
            if (canvas && video) {
                canvas.style.width = video.offsetWidth + "px";
                canvas.style.height = video.offsetHeight + "px";
                canvas.style.top = video.offsetTop + "px";
                canvas.style.left = video.offsetLeft + "px";
            }
        });
    </script>
</body>
</html>