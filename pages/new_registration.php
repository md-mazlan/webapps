<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Step Registration Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #374151;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        /* Main Container */
        .main-container {
            width: 100%;
            max-width: 56rem; /* 896px */
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Steps Container (Desktop) */
        .steps-container {
            background-color: #f9fafb;
            padding: 2rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .steps-container h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 2rem;
        }

        .steps-wrapper {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .progress-step-desktop {
            position: relative;
            transition: all 0.4s ease-in-out;
        }

        .step-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .step-circle {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px; /* full */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            font-weight: 600;
            z-index: 10;
            transition: all 0.4s ease-in-out;
            background-color: white;
            color: #6B7280;
            border: 2px solid #D1D5DB;
            font-family: Consolas, 'Courier New', monospace;
        }

        .step-label h3 {
            font-weight: 600;
        }

        .step-label p {
            font-size: 0.875rem;
        }
        
        .progress-step-desktop .step-label {
            transition: all 0.4s ease-in-out;
            color: #6B7280;
        }

        .progress-step-desktop.active .step-circle {
            background-color: #3B82F6;
            color: white;
            border-color: #3B82F6;
        }

        .progress-step-desktop.active .step-label {
            color: #1F2937;
            font-weight: 600;
        }

        .progress-step-desktop.completed .step-circle {
            background-color: #10B981;
            color: white;
            border-color: #10B981;
        }

        .progress-step-desktop.completed .step-label {
             color: #374151;
        }

        .progress-step-desktop:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 1.25rem;
            top: 2.5rem;
            height: calc(100% + 2rem);
            width: 2px;
            background-color: #E5E7EB;
            z-index: 1;
        }

        .progress-step-desktop.completed::after {
            background-color: #10B981;
        }

        /* Form Container */
        .form-container {
            width: 100%;
            padding: 1.5rem;
        }

        /* Mobile Progress Bar */
        .mobile-progress-bar {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .mobile-progress-line-bg {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #e5e7eb;
            transform: translateY(-50%);
        }
        #progress-line-mobile {
            height: 100%;
            background-color: #3b82f6;
            transition: width 0.5s ease-in-out;
        }
        .mobile-steps-wrapper {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .progress-step-mobile .step-circle {
            width: 2rem;
            height: 2rem;
            font-size: 0.875rem;
        }

        /* Form Content */
        .form-wrapper {
            overflow: hidden;
        }

        #registration-form {
            display: flex;
            transition: transform 0.5s ease-in-out;
            min-height: 450px;
        }

        .form-step {
            width: 100%;
            flex-shrink: 0;
            padding: 0 0.5rem;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1.5rem;
        }

        .form-grid {
            display: grid;
            gap: 1.5rem;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
        }
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: transparent;
            box-shadow: 0 0 0 2px #3b82f6;
        }

        /* Buttons */
        .button-container {
            margin-top: 2rem;
            padding-top: 1.25rem;
            display: flex;
            justify-content: space-between;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            transition: background-color 0.3s;
        }

        .btn-secondary {
            background-color: #d1d5db;
            color: #374151;
        }
        .btn-secondary:hover {
            background-color: #9ca3af;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
            margin-left: auto;
        }
        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-success {
            background-color: #10b981;
            color: white;
        }
        .btn-success:hover {
            background-color: #059669;
        }

        /* Responsive Styles */
        @media (min-width: 768px) {
            .main-container {
                flex-direction: row;
            }
            .steps-container {
                display: block;
                width: 33.333333%;
                border-right: 1px solid #e5e7eb;
                border-bottom: none;
            }
            .form-container {
                width: 66.666667%;
                padding: 2rem;
            }
            .mobile-progress-bar {
                display: none;
            }
            .form-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .grid-col-span-2 {
                grid-column: span 2 / span 2;
            }
        }
        @media (max-width: 767px) {
            .steps-container {
                display: none;
            }
        }

    </style>
</head>
<body>

    <div class="main-container">

        <!-- Left Pane: Desktop Step Indicators -->
        <div class="steps-container">
            <h2>Pendaftaran</h2>
            <div class="steps-wrapper">
                <div id="step-desktop-1" class="progress-step-desktop active">
                    <div class="step-content">
                        <div class="step-circle">1</div>
                        <div class="step-label">
                            <h3>Maklumat Peribadi</h3>
                            <p>Sila lengkapkan butiran anda.</p>
                        </div>
                    </div>
                </div>
                <div id="step-desktop-2" class="progress-step-desktop">
                    <div class="step-content">
                        <div class="step-circle">2</div>
                        <div class="step-label">
                            <h3>Maklumat Pekerjaan</h3>
                            <p>Butiran pekerjaan anda.</p>
                        </div>
                    </div>
                </div>
                <div id="step-desktop-3" class="progress-step-desktop">
                    <div class="step-content">
                        <div class="step-circle">3</div>
                        <div class="step-label">
                            <h3>Pilihan Ahli</h3>
                            <p>Lengkapkan pendaftaran.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Pane: Form Content -->
        <div class="form-container">
            <!-- Mobile Progress Bar -->
            <div class="mobile-progress-bar">
                <div class="mobile-progress-line-bg">
                    <div id="progress-line-mobile" style="width: 0%;"></div>
                </div>
                <div class="mobile-steps-wrapper">
                    <div id="step-mobile-1" class="progress-step-mobile active"><div class="step-circle">1</div></div>
                    <div id="step-mobile-2" class="progress-step-mobile"><div class="step-circle">2</div></div>
                    <div id="step-mobile-3" class="progress-step-mobile"><div class="step-circle">3</div></div>
                </div>
            </div>

            <div class="form-wrapper">
                <form id="registration-form">
                    <!-- Step 1: Personal Details -->
                    <div class="form-step">
                        <h2 class="form-title">Maklumat Individu</h2>
                        <div class="form-grid">
                            <input type="text" placeholder="Nama" class="form-input">
                            <input type="text" placeholder="No Kad Pengenalan" class="form-input">
                            <select class="form-select">
                                <option disabled selected>Jantina</option>
                                <option>Lelaki</option>
                                <option>Perempuan</option>
                            </select>
                            <input type="text" placeholder="Bangsa (cth: Etnik Sabah)" class="form-input">
                            <input type="tel" placeholder="No Telefon" class="form-input">
                            <input type="email" placeholder="Email" class="form-input">
                            <input type="text" placeholder="Alamat Baris 1" class="form-input grid-col-span-2">
                            <input type="text" placeholder="Alamat Baris 2" class="form-input grid-col-span-2">
                            <input type="text" placeholder="Daerah" class="form-input">
                            <input type="text" placeholder="Poskod" class="form-input">
                            <select class="form-select grid-col-span-2">
                                <option disabled selected>Negeri</option>
                                <option>Johor</option><option>Kedah</option><option>Kelantan</option><option>Melaka</option><option>Negeri Sembilan</option><option>Pahang</option><option>Pulau Pinang</option><option>Perak</option><option>Perlis</option><option>Sabah</option><option>Sarawak</option><option>Selangor</option><option>Terengganu</option><option>W.P. Kuala Lumpur</option><option>W.P. Labuan</option><option>W.P. Putrajaya</option>
                            </select>
                            <input type="text" placeholder="Kawasan Mengundi" class="form-input grid-col-span-2">
                        </div>
                    </div>

                    <!-- Step 2: Employment Details -->
                    <div class="form-step">
                        <h2 class="form-title">Maklumat Pekerjaan</h2>
                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <select class="form-select">
                                <option disabled selected>Jenis Pekerjaan</option><option>Kerajaan</option><option>Swasta</option><option>Persendirian</option>
                            </select>
                            <input type="text" placeholder="Nama Majikan" class="form-input">
                            <input type="text" placeholder="Alamat Tempat Kerja" class="form-input">
                        </div>
                    </div>

                    <!-- Step 3: Membership Details -->
                    <div class="form-step">
                        <h2 class="form-title">Pilihan Ahli</h2>
                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                             <input type="text" placeholder="Pilihan Ahli Kawasan Berkhidmat (cth: DUN Api-Api)" class="form-input">
                            <select class="form-select">
                                <option disabled selected>Saiz Baju / Vest</option><option>S</option><option>M</option><option>L</option><option>XL</option><option>XXL</option><option>XXXL</option>
                            </select>
                            <select class="form-select">
                                <option disabled selected>Jenis Pembayaran</option><option>Pendaftaran</option><option>Pembaharuan</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Navigation Buttons -->
            <div class="button-container">
                <button type="button" id="prev-btn" class="btn btn-secondary" style="display: none;">Sebelum</button>
                <button type="button" id="next-btn" class="btn btn-primary">Seterusnya</button>
                <button type="button" id="submit-btn" class="btn btn-success" style="display: none;">Serah</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');
            const registrationForm = document.getElementById('registration-form');
            const progressStepsDesktop = document.querySelectorAll('.progress-step-desktop');
            const progressStepsMobile = document.querySelectorAll('.progress-step-mobile');
            const progressLineMobile = document.getElementById('progress-line-mobile');
            
            let currentStep = 1;
            const totalSteps = progressStepsDesktop.length;

            const updateSliderPosition = () => {
                const offset = -(currentStep - 1) * 100;
                registrationForm.style.transform = `translateX(${offset}%)`;
            };

            const updateProgressBar = () => {
                // Update Desktop Steps
                progressStepsDesktop.forEach((step, index) => {
                    const stepNumber = index + 1;
                    step.classList.remove('active', 'completed');
                    if (stepNumber < currentStep) {
                        step.classList.add('completed');
                    } else if (stepNumber === currentStep) {
                        step.classList.add('active');
                    }
                });

                // Update Mobile Steps
                progressStepsMobile.forEach((step, index) => {
                    step.classList.remove('active', 'completed');
                    if (index < currentStep) {
                        step.classList.add('completed');
                    } else if (index + 1 === currentStep) {
                        step.classList.add('active');
                    }
                });
                
                // Update mobile progress line width
                const progressWidth = ((currentStep - 1) / (totalSteps - 1)) * 100;
                progressLineMobile.style.width = `${progressWidth}%`;
            };

            const updateButtons = () => {
                prevBtn.style.display = currentStep > 1 ? 'inline-block' : 'none';
                nextBtn.style.display = currentStep < totalSteps ? 'inline-block' : 'none';
                submitBtn.style.display = currentStep === totalSteps ? 'inline-block' : 'none';
            };

            const goToStep = (step) => {
                currentStep = step;
                updateSliderPosition();
                updateProgressBar();
                updateButtons();
            };

            nextBtn.addEventListener('click', () => {
                if (currentStep < totalSteps) {
                    goToStep(currentStep + 1);
                }
            });

            prevBtn.addEventListener('click', () => {
                if (currentStep > 1) {
                    goToStep(currentStep - 1);
                }
            });
            
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                document.getElementById(`step-desktop-${totalSteps}`).classList.add('completed');
                document.getElementById(`step-desktop-${totalSteps}`).classList.remove('active');
                document.getElementById(`step-mobile-${totalSteps}`).classList.add('completed');
                document.getElementById(`step-mobile-${totalSteps}`).classList.remove('active');
                
                const formContainer = document.querySelector('.form-container');
                formContainer.innerHTML = `
                    <div style="text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; min-height: 450px;">
                         <svg style="width: 6rem; height: 6rem; color: #10b981; margin-bottom: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h2 style="font-size: 1.875rem; font-weight: 700; color: #1f2937;">Pendaftaran Berjaya!</h2>
                        <p style="color: #4b5563; margin-top: 0.5rem;">Terima kasih kerana mendaftar. Kami akan menghubungi anda tidak lama lagi.</p>
                    </div>
                `;
            });

            // Initial setup
            updateProgressBar();
            updateButtons();
        });
    </script>
</body>
</html>
