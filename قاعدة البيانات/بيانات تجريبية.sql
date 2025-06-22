--بيانات تجريبية

USE clinic_db;

-- مستخدم إداري
INSERT INTO users (name, email, password_hash, role) VALUES 
('Admin User', 'admin@clinic.com', '$2y$10$5pSrtR1DR6Z4l7T86nqkUOhE5Oj/77VT5nMPkJGPtkXyRnbK2FRe', 'admin');

-- طبيب تجريبي
INSERT INTO users (name, email, password_hash, role) VALUES 
('Dr. Ahmed', 'doctor@clinic.com', '$2y$10$5pSrtR1DR6Z4l7T86nqkUOhE5Oj/77VT5nMPkJGPtkXyRnbK2FRe', 'doctor');

INSERT INTO doctors (user_id, specialty, bio, consultation_fee) VALUES 
((SELECT id FROM users WHERE email='doctor@clinic.com'), 'أمراض القلب', 'طبيب متخصص في أمراض القلب والأوعية الدموية.', 200.00);

-- مريض تجريبي
INSERT INTO users (name, email, password_hash, role) VALUES 
('Ali Patient', 'patient@clinic.com', '$2y$10$5pSrtR1DR6Z4l7T86nqkUOhE5Oj/77VT5nMPkJGPtkXyRnbK2FRe', 'patient');

INSERT INTO patients (user_id, blood_type, chronic_diseases) VALUES 
((SELECT id FROM users WHERE email='patient@clinic.com'), 'A+', 'ضغط دم عالي');

-- موعد تجريبي
INSERT INTO appointments (patient_id, doctor_id, appointment_date, status) VALUES 
(
  (SELECT id FROM users WHERE email='patient@clinic.com'), 
  (SELECT id FROM users WHERE email='doctor@clinic.com'),
  DATE_ADD(NOW(), INTERVAL 2 DAY),
  'pending'
);

-- سجل طبي تجريبي
INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment) VALUES 
(
  (SELECT id FROM users WHERE email='patient@clinic.com'), 
  (SELECT id FROM users WHERE email='doctor@clinic.com'),
  'تشخيص مبدئي: ارتفاع ضغط الدم',
  'الاستمرار في المتابعة الدورية وتناول الدواء الموصوف'
);
