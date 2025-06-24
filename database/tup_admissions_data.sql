-- DATA-ONLY SQL DUMP FOR TUP ADMISSIONS
-- Contains only INSERT INTO ... VALUES ... statements for all tables
-- Generated from tup_admissions_new-finalDB.sql

-- Table: activity_log
INSERT INTO `activity_log` (`log_id`, `user_id`, `action`, `created_at`) VALUES
(1, 1, 'Added new exam schedule ID: 1', '2025-05-30 13:56:53'),
(2, 1, 'Closed exam schedule ID: 1', '2025-05-30 13:57:11'),
(3, 1, 'Added new exam schedule ID: 2', '2025-05-30 13:57:28'),
(4, 1, 'Closed exam schedule ID: 1', '2025-05-30 13:57:29'),
(5, 2, 'exam_registration', '2025-05-30 13:57:36'),
(6, 1, 'Updated exam score for registration ID: 1', '2025-05-30 13:58:00'),
(7, 2, 'login', '2025-05-30 15:03:55'),
(8, 3, 'login', '2025-05-30 15:07:49'),
(9, 1, 'login', '2025-05-30 15:08:14'),
(10, 1, 'Updated exam score for registration ID: 1', '2025-05-30 15:44:00'),
(11, 17, 'exam_registration', '2025-06-02 04:33:08'),
(12, 1, 'Added new exam schedule ID: 3', '2025-06-02 15:23:50'),
(13, 31, 'exam_registration', '2025-06-02 15:24:59'),
(14, 38, 'exam_registration', '2025-06-02 16:09:55'),
(15, 1, 'Closed exam schedule ID: 3', '2025-06-02 16:13:03'),
(16, 1, 'Closed exam schedule ID: 2', '2025-06-02 16:18:58'),
(17, 1, 'Added new exam schedule ID: 4', '2025-06-02 16:24:41'),
(18, 38, 'exam_registration', '2025-06-02 16:25:58'),
(19, 1, 'Marked exam schedule ID: 4 as completed', '2025-06-02 16:29:17'),
(0, 1, 'Added new exam schedule ID: 0', '2025-06-12 03:44:13');

-- applicants
INSERT INTO `applicants` (`applicant_id`, `user_id`, `first_name`, `middle_name`, `last_name`, `birth_date`, `gender`, `mobile_number`, `address_lot`, `address_street`, `address_town`, `address_city`, `address_country`, `address_zipcode`, `mother_maiden_name`, `father_name`, `elementary_school`, `elementary_year_graduated`, `high_school`, `high_school_year_graduated`, `primary_program_id`, `secondary_program_id`) VALUES
(4, 19, 'Mitz', 'Baniasia', 'Benignos', '2006-08-24', 'female', '09666359133', '', 'Quezon Street', 'Poblacion', 'Toboso', 'Philippines', '6125', 'Enriqueta A. Baniasia', 'Cirilo A. Benignos', 'Toboso Central School', '2019', 'Toboso National High School', '2023', 1, 3),
(5, 20, 'Yuan Miguel', 'Villason', 'Blanca', '2006-12-08', 'male', '09196119381', 'N/A', 'Felix Amante Ave.', 'Brgy. Banago', 'Bacolod City', 'Philippines', '6100', 'Rhea Villason Blanca', 'Engr. Marco Torrecampo Blanca', 'University of St. La Salle', '0000', 'University of St. La Salle', '0000', 1, 4),
(6, 21, 'Mikylla', 'Gumban', 'Biatin', '2007-09-30', 'female', '09668658985', '', 'Prk. Santan', 'Tampalon', 'Kabankalan', 'Philippines', '6111', 'Gigi A Gumban', 'Michael G. BIatin', 'Tampalon Elementary', '2019', 'Southland College', '2024', 1, 2),
(7, 22, 'Carl Julian', 'Mijares', 'Brizuela', '2006-07-16', 'male', '09672958594', '', 'St. Nicolas Street', 'Brgy. Sto. Rosario', 'Binalbagan', 'Philippines', '6107', 'Catherine Mijares Brizuela', 'Roque Brizuela', 'Binalbagan Elementary School', '2018', 'Binalbagan Catholic Colllege Inc.', '2025', 1, 2),
(8, 23, 'Anne Quesia', 'Tirasol', 'Calderon', '2006-10-25', 'female', '09924540126', '', 'Purok Sibuyas', 'Gil Montilla', 'Sipalay City', 'Philippines', '6113', 'Ma. Leah Llena Solidarios Tirasol', 'Darren Villo Calderon', '', '0000', '', '0000', 1, 6),
(9, 24, 'Czaren BL', 'Berja', 'Canlog', '2007-07-24', 'female', '09919029427', 'Bacolod City', 'Purok 4 Magsungay', 'Brgy. Singcang-Airport', 'Bacolod', 'Philippines', '6100', 'Oneale T. Berja', 'Clark G. Canlog', 'Education and Training Center School III', '2019', 'Negros Occidental High School', '2025', 1, 2),
(10, 25, 'John Aldrich', 'Geruldo', 'Cabras', '2006-12-31', 'male', '09515880214', 'N/A', 'Prk. Masinadyahon', 'Brgy. Rizal', 'Sagay City', 'Philippines', '6122', 'Mary Ann O. Geruldo', 'Johndale T. Cabras', 'Alfredo E. Maranon Elementary School - School of the Future', '2019', 'Sagay National High School', '2025', 1, 4),
(11, 26, 'Kennby', 'Apurado', 'Cabrestante', '2006-08-03', 'male', '09305933509', '', 'purok Kawilihan', 'Brgy. DJLA', 'Bago City', 'Philippines', '6101', 'Myrflortess C. Apurado', 'Kenny G. Cabrestante', 'Ma-ao Sugar Central Elementary School', '2018', 'Ramon Torres Ma-ao Sugar Central National High School', '2023', 1, 2),
(12, 27, 'Angela Therese', 'Diomon', 'Capunong', '2006-10-07', 'female', '09158770363', 'Block 25 Lot 20', 'Sampaguita street', 'Glendale Homes, BRGY. Granada', 'Bacolod', 'Philippines', '6100', 'Diomon', 'Elbert T. Capunong', 'Jack and Jill School Homesite', '2019', 'St. Joseph School La Salle', '2023', 1, 2),
(13, 28, 'Charise', 'Georsua', 'Caratao', '2007-02-01', 'female', '09670520766', '35', 'Tearfund Kalipay Village', 'Cadiz Viejo', 'Cadiz', 'Philippines', '6121', 'Donna Georsua', 'Orly Caratao', 'Cadiz Viejo Elementary School', '2018', 'Sped High School', '2025', 1, 2),
(14, 29, 'Andrea Nicole', 'Adorio', 'Catapang', '2006-05-29', 'female', '09563998609', 'Blk 4 Lt 14', 'Eagle Drive Street', 'Brgy. Estefania', 'Bacolod', 'Philippines', '6100', 'Ma. Theresa Dumada-ug Adorio', 'Zaldy Lumatac Catapang', 'St.Scholastica\'s Academy - Bacolod', '2019', 'Don Bosco Technical Institute - Victorias', '2023', 1, 4),
(15, 30, 'Alyanna', 'Tranquillero', 'Chavez', '2007-10-07', 'female', '09158481196', 'N/A', 'PRK. MAINUSWAGON', 'BRGY. ATIPULUAN', 'BAGO CITY', 'Philippines', '6101', 'ANNABELLE C. TRANQUILLERO', 'JIMUEL SR. O. CHAVEZ', 'PHILIPPINE LUMEN SCHOOL', '2018', 'SUM-AG NATIONAL HIGH SCHOOL', '2024', 1, 2),
(16, 31, 'MARJHON', 'BONGANAY', 'CIRIACO', '2006-06-14', 'male', '09663518106', 'N/A', 'PUROK ALOGBATI', 'BARANGAY GIL MONTILLA', 'SIPALAY', 'Philippines', '6113', 'MARGIE BONGANY', 'JOEL M.  CIRIACO', 'GIL M MONTILLA ELEMENTARY SCHOOL', '2019', 'GIL MONTILLA NATIONAL HIGH SCHOOL', '2025', 1, 10),
(17, 32, 'KAIZA', 'CASTILLO', 'COSTOY', '2006-11-11', 'female', '09815761495', '', 'BONIFACIO EXTENSION', 'ROBLES', 'LA CASTELLANA', 'Philippines', '6131', 'KRISTY  M. CASTILLO', 'PELAGIO J. COSTOY JR.', 'DON FELIX ROBLES ELEMENTARY SCHOOL', '2019', 'DONA HORTENCIA SALAS BENEDICTO NATIONAL HIGH SCHOOL SENIOR HIGH SCHOOL', '2025', 1, 2),
(18, 33, 'Lawrence Gabriel', 'Gever', 'Cruz', '2006-09-12', 'male', '+63 9060256508', 'Blk12 Lot34', '-', 'Alijis', 'Bacolod', 'Philippines', '6100', 'Nieves Gever Cruz', 'Loreto Cruz Jr.', 'St. John\'s Institute', '2018', 'St. John\'s Institute', '2023', 1, 3),
(19, 34, 'TEDDY', 'MAGBANUA', 'DE LA TORRE', '2007-02-10', 'male', '09364402564', '', 'SITIO AGTONGTONG', 'BRGY TORTOSA', 'MANAPLA', 'Philippines', '6120', 'GLORY MAGBANUA', '', 'TORTOSA ELEMENTARY SCHOOL', '2018', 'SAINT ROCH ACADEMY', '2022', 1, 4),
(20, 35, 'Joshua Miguel', 'Perez', 'Consorte', '2007-02-04', 'male', '09632704946', 'Lot 28', 'Carmen Street', 'Barangay 19-A', 'Victorias City', 'Philippines', '6119', 'Ma. Consuelo G. Perez', 'Bobby N. Consorte', 'Don Bosco Technical Institute of Victorias, Incorporated', '2019', 'Don Bosco Technical Institute of Victorias, Incorporated', '2023', 1, 6),
(21, 36, 'Sean Caine', 'Tibus', 'Demaisip', '2006-11-01', 'male', '09997813452', 'Bacolod City', 'Meadows of Camelot Subd.', 'Barangay Estefania', 'Bacolod', 'Philippines', '6100', 'Beryl Pratt Tibus', 'Jo Gonzaga Demaisip', 'Estefania Elementary School', '2019', 'Maranatha Christian College', '2023', 1, 2),
(22, 37, 'Rey Gabriel', 'Zaragoza', 'Dela Cruz', '2007-10-30', 'male', '+639567824892', 'NA', 'Prk. Ramos', 'Barangay Dulao', 'Bago City', 'Philippines', '6101', 'Brenda Libo-on Zaragoza', 'Antonio Bahalan Dela Cruz', 'Sum-ag Elementary School', '2018', 'Sum-ag National High School', '2022', 1, 2),
(23, 40, 'Rey Gabriel', 'Zaragoza', 'Dela Cruz', '2007-10-30', 'male', '09567824892', 'NA', 'Prk. Ramos', 'Brgy. Dulao', 'Bago City', 'Philippines', '6101', 'Brenda Libo-on Zaragoza', 'Antonio Bahalan Dela Cruz', 'Sum-ag Elementary School', '2018', 'Sum-ag National High School', '2024', 1, 2),
(24, 41, 'Gabriel', 'Valdivia', 'Delera', '2007-01-17', 'male', '09549802592', 'N/A', 'Houston street', 'Barangay 1', 'Kabankalan City', 'Philippines', '', 'Crishel V. Valdivia', 'Henry Perfecto S. Delera', 'Don Bosco Technical Institute Victorias and Fortress college', '2019', 'Talubangi National Highschool', '2025', 1, 2),
(25, 42, 'Ron Mariz', 'Eduardo', 'de la Torre', '2007-08-13', 'female', '09665043770', 'Bacolod City', 'n/a', 'Brgy.Tangub', 'Bacolod', 'Philippines', '6100', 'Ailyn P. Eduardo', 'Rodolfo J. de la Torre', 'Rodolfo A. Medel Elementary School', '2017', 'Luisa Medel National High School', '2022', 1, 3),
(26, 43, 'Cris Niccolo', 'Valencia', 'De Leon', '2006-01-12', 'male', '09394246396', 'L10 B58', 'Phase 4', 'Zone 15', 'Talisay City', 'Philippines', '6115', 'Vanessa V. Valencia', 'Cristopher V. De Leon', 'Montessori Elementary School', '2013', 'Colegio San Agustin - Bacolod', '2023', 1, 3),
(27, 44, 'Anthony Art', 'Achurra', 'Demegillo', '2006-03-07', 'male', '09489459288', 'No.44', 'Malunsi Subd.', 'Brgy Zone 3', 'Talisay City', 'Philippines', '6115', 'Aurabelle Achurra', 'Marco Antonio Demegillo', 'St. Scholastica\'s Academy Bacolod', '2019', 'St. Scholastica\'s Academy Bacolod', '2025', 1, 12),
(28, 45, 'Matt Laurent', 'Batoon', 'Diaz', '2007-02-11', 'male', '09150178939', 'N/A', 'Sitio pulo luguay', 'Balaring', 'Silay', 'Philippines', '6116', 'Melanie Magnaye Batoon', 'Rommel Diaz', 'St.Francis Of Assisi School', '2019', 'St.Francis Of Assisi School', '2023', 1, 2),
(29, 46, 'Jude Steven', 'Hidalgo', 'Doromal', '2006-09-16', 'male', '09916773332', 'Lot 43 Block  3', 'Fermont Village', 'Barangay 7', 'Victorias City', 'Philippines', '6119', 'Mae D. Hidalgo', 'Steve B. Doromal', 'Colegio De Sta. Ana de Victorias - Integrated School', '0000', 'Don Bosco Technical Institute - Victorias', '0000', 1, 3),
(30, 47, 'Fritz Ysabelle', '', 'Doloso', '2007-06-05', 'female', '09060026054', 'Agramon Apartment', 'Rizal St.', 'Brgy. Zone 9', 'Talisay City', 'Philippines', '6115', 'Daisy Testa Doloso', '', 'Payao Elementary School', '2019', 'Bacolod City National High School', '2023', 1, 2),
(31, 48, 'Jaylord', 'Cantong', 'Dueñas', '2007-06-09', 'male', '09120792823', 'NA', 'prk. 17', 'La Granja', 'La Carlota City', 'Philippines', '6130', 'Noemi', 'Herman', 'La Granja Elementary School', '0000', 'Doña Hortencia Salas Benedicto NHS', '0000', 1, 2),
(32, 49, 'Ronnel', 'Tupal', 'Esmillaren', '2007-01-30', 'male', '09928855016', '6100', 'Purok Santol', 'Barangay Mandalagan', 'Bacolod City', 'Philippines', '6100', 'Mary Bless Dominguez Tupal', 'Ronilo Milos Esmillaren', 'St. Scholastica\'s Academy Bacolod', '0000', 'St. Joseph School- La Salle', '0000', 1, 3),
(33, 50, 'Erika Lyn', 'Magbato', 'Erillo', '2006-09-02', 'female', '+639392573298', '', 'Purok 7 Manayanaya', 'Barangay Dancalan', 'Ilog', 'Philippines', '6109', 'Edily L. Magbato', 'Tresanto T. Erillo', 'A.L. Jayme Elementary School', '2019', 'Southland College', '2025', 1, 3),
(34, 51, 'Erich Grace', 'Reyes', 'Dupitas', '2005-11-06', 'female', '09319153991', 'Dona Juliana', 'Las Palmas, Prk. Jardin', 'Brgy. Taculing', 'Bacolod', 'Philippines', '6100', 'Elizabeth G. Reyes', 'Junver U. Dupitas', 'Tabucan Elementary School', '2018', 'Negros Occidental High School', '2024', 1, 4),
(35, 52, 'Styrene', 'Dela Gracia', 'Esparcia', '2006-11-27', 'female', '09279244246', 'Bacolod City', '', 'Taculing', 'Bacolod', 'Philippines', '6100', 'Richel Dela Gracia', 'Alfred C. Esparcia', 'Apolinario Mabini Elementary School', '2019', 'Negros Occidental High School', '2025', 1, 9),
(36, 53, 'Joshua Kendric', 'Go', 'Espartero', '2006-09-16', 'male', '09664930474', 'Block 18 Lot 14&16', 'Amazon Street', 'Vista Alegre', 'Bacolod City', 'Philippines', '6100', 'Girlie Ricafort Go', 'Irwin Corgos Espartero', 'Bacolod Trinity Christian School Inc.', '2017', 'Bacolod Trinity Christian School Inc.', '2025', 1, 4),
(37, 54, 'Joules Isaac', 'Despojo', 'Guntalidad', '2006-11-24', 'male', '09709010152', 'Bacolod City', 'vera street', 'Brgy. Puntataytay', 'Bacolod', 'Philippines', '6100', 'Juvy D. Guntalidad', 'Joel B. Guntalidad', 'FR. FLORES ELEMENTARY SCHOOL', '0000', 'SUM AG NATIONAL HIGH SCHOOL', '0000', 1, 2),
(38, 55, 'Irene Marie', 'De Guzman', 'Hermosura', '2006-10-02', 'female', '09811798928', '', 'Mango street', 'Barangay 5', 'Silay City', 'Philippines', '6116', 'Ma. Theresa G. De Guzman', 'Ramon P. Hermosura Jr.', 'Silay South Elementary School', '0000', '2017', '2023', 1, 3),
(39, 56, 'Ellaine Raquel', 'Jacildo', 'Guzon', '2006-11-25', 'female', '09935864457', '', 'Malecon Street', 'Brgy. Zone 6', 'Pulupandan', 'Philippines', '6102', 'Leah L. Jacildo', 'Russel R. Guzon', 'Pulupandan Praise Christian Community School', '2019', 'Enriqueta Montilla de Esteban Memorial High School', '2025', 1, 8),
(40, 57, 'Jessie', 'Jaleco', 'Jalandra', '2007-09-22', 'male', '09158757403', '', 'Remedios street', 'Barangay - 19A', 'Victorias City', 'Philippines', '6119', 'Rubie Pearl Jaleco', 'Jesus Jalandra Jr.', 'Victorias Milling Elementary School', '2018', 'Don Bosco Technical Institue of Victorias Inc.', '2024', 1, 8),
(41, 58, 'Nicole', 'Teja', 'Jalique', '2006-07-21', 'female', '09630135187', 'Hda. San Isidro', '', 'Brgy. Bagtic', 'Silay City', 'Philippines', '6116', 'Eden S. Teja', 'Ulysis M. Jalique', 'Silay South Elementary School', '2019', 'Dona Montserrat Lopez Memorial High School', '2025', 1, 4),
(42, 59, 'Chenest Anne', 'Detoyato', 'Java', '2006-10-31', 'female', '09455627639', 'n/a', 'De Leon Street', 'Barangay 2', 'Victorias City', 'Philippines', '6119', 'Cherryl P. Detoyato', 'Ernesto F. Java, Jr.', 'Victorias Elementary School', '2019', 'Negros Occidental National Science High School', '2023', 1, 3),
(43, 60, 'Princess Nicole', 'Daguno', 'Jimenez', '2007-03-30', 'female', '09855628612', 'Bacolod City', 'Bayanihan 1', 'Punta Taytay', 'Bacolod', 'Philippines', '6100', 'Khrisna M. Daguno', 'Jay P. Jimenez', 'F. R. Flores Elementary School', '2019', 'Holy Familly High School Bacolod', '2023', 1, 4),
(44, 61, 'John Chris', 'Pineda', 'Jimenez', '2007-04-26', 'male', '09289299095', 'Bacolod City', 'Magbinuligay', 'Bata', 'Bacolod', 'Philippines', '6100', 'Christine B. Pineda', 'John Dexter P. Jimenez', 'Bata Elementary School - 1', '2019', 'Bata National Highschool', '2023', 1, 4),
(45, 62, 'Prince angelo', 'roquez', 'lambo on', '2007-01-29', 'male', '09915941926', '', 'brgy.sanjose', 'brgy san jose', 'EB. magalona', 'Philippines', '6118', 'geraldine lamboon', 'francisco Lambo on', '', '0000', '', '0000', 1, 3),
(46, 63, 'Juntri', 'Abesia', 'Langreo', '2007-06-03', 'male', '09605111117', 'Block 10 Lot 11', 'Menlo Heights', 'Zone 10', 'Talisay City', 'Philippines', '6115', 'Lea A. Abesia', 'Remy F. Langreo', 'Talisay Elementary School', '2019', 'Rafael B. Lacson Memorial High School', '2025', 1, 2),
(47, 64, 'Jhuliana Mae', 'Dela Cruz', 'Lirazan', '2007-10-20', 'female', '09947952793', 'N/A', 'Purok Paglaum', 'Brgy. Dancalan', 'Municipality of Ilog', 'Philippines', '6109', 'Meralona Dalmacio Dela Cruz', 'Julie Lozana Lirazan', 'Filomena G. Gequillana Elementary School', '2019', 'Kabankalan Catholic College Inc.', '2025', 9, 8),
(48, 65, 'Trisha', 'Gelogo', 'Loberas', '2007-08-23', 'female', '09944519510', '', 'Sitio Bugnay', 'Daan Banua', 'Kabankalan City', 'Philippines', '6111', 'Gemma I. Gelogo', 'Edgar C. Loberas', 'Daan Banua Elementary School', '2019', 'Kabankalan Catholic College, Inc.', '2025', 1, 2),
(49, 66, 'Julianne May', 'Remo', 'Lodovise', '2007-05-15', 'female', '09934133958', '', 'Purok 1', 'Guinpana-an', 'Moises Padilla', 'Philippines', '6132', 'Violy Jane A. Remo', 'Mario C. Lodovise', 'Guinpana-an Elementary School', '2018', 'University of Negros Occidental-Recoletos', '2025', 1, 4),
(50, 67, 'Shea', 'Labiana', 'Mabaquiao', '2025-07-15', 'female', '09073612174', 'NA', 'Hda. Canibungan', 'Barngay Luna', 'Cadiz City', 'Philippines', '6121', 'Angelie D. Labiana', 'Carlos D. Mabaquiao Jr.', 'SPED TRAINING CENTER', '2019', 'Dr. Vicente F. Gustilo Memorial National Highschool', '2025', 1, 2),
(51, 68, 'Angel', 'Palomo', 'Mag-usara', '2006-10-27', 'female', '09397057797', '123', 'Lacson St. Ext.', 'Barangay 40', 'Bacolod', 'Philippines', '6100', 'Ligaya D. Palomo', 'Noli S. Mag-usara', 'Eliakim Learning Center', '2019', 'Negros Occidental High School', '2025', 1, 2),
(52, 69, 'Arlo', 'Palabrica', 'Magno', '2007-10-10', 'male', '09388088482', '6118', 'Hda. Carmelo', 'Sto. Nino', 'E.B.Magalona', 'Philippines', '6118', 'Julla M. Palabrica', 'Ricardo S. Magno', 'Don H. Maravilla Memorial School', '2019', 'E.B.Magalona National Highschool', '2025', 1, 2),
(53, 70, 'Cham Gazelle', 'Balo', 'Mallo', '2006-11-05', 'female', '09914717069', 'Bacolod City', 'Lizares', 'Barangay 18', 'Bacolod', 'Philippines', '6100', 'Elizabeth B. Mallo', 'Garry L. Mallo', 'ETCS-1', '2019', 'UNO-R', '2025', 1, 4),
(54, 71, 'Enrique Gabriel', 'Jugos', 'Manual', '2007-01-29', 'male', '09279510714', 'Lot 13, Blk.9', 'N/A', 'Brgy. 5', 'Silay City', 'Philippines', '6116', 'Annabel Ubarra Jugos', 'Noli Balagosa Manual', 'St. Theresitas Academy', '2019', 'Negros Occidental National Science High School', '2025', 1, 2),
(55, 72, 'Earl Floyd', 'Roma', 'Mapisa', '2006-10-07', 'male', '09922524392', '00120', 'Purok 1', 'Brgy. Guinpana-an', 'Moises Padilla', 'Philippines', '6132', 'Flordeliza R. Mapisa', 'Ernesto N. Mapisa Jr.', 'Guinpana-an Elementary School', '2019', 'Cabacungan National High School', '2025', 1, 4),
(56, 73, 'Fermin III', 'Seva', 'Maranga', '2006-10-31', 'male', '09500357526', 'N/A', 'N/A', 'Barangay 13', 'Victorias City', 'Philippines', '6119', 'Shirley Seva', 'Fermin Maranga Jr.', 'Victorias Elementary School', '2018', 'Don Bosco Technical Institute', '2025', 1, 3),
(57, 74, 'Richard John', 'Martinete', 'Mula', '2007-01-22', 'male', '09914733102', 'N/A', 'Von Ryan', 'Barangay Zone 6', 'Pulupandan', 'Philippines', '6102', 'Josephine E. Martinite', 'Lemuel G. Mula', 'Pulupandan Elementary School', '2018', 'Enriqueta Montilla De Esteban Memorial High School', '2024', 1, 4),
(58, 75, 'kristian Paul', 'Marpa', 'Monton', '2007-03-04', 'male', '09129642058', 'N/A', 'Lopez jeana 2', 'Brgy.Talaban', 'Himamaylan City', 'Philippines', '6108', 'Erma M. Monton', 'Ronnie Monton', 'Phillipine Normal University', '2023', 'University of Negros Occidental Recoletos', '2025', 1, 4),
(59, 76, 'John Rex', 'Elumba', 'Mongcal', '2006-11-13', 'male', '09939185677', 'N/A', 'Phase 1-A, Purok Housing', 'Brgy. Canroma', 'Pontevedra', 'Philippines', '6105', 'Divina Elumba', 'Edwin Mongcal', 'Pontevedra North Elementary School', '2019', 'Hinigaran National High School', '2025', 1, 6),
(60, 77, 'Mikan', 'Pacificador', 'Minada', '2007-02-23', 'female', '09468475413', 'Lot 7 blk 2', 'B Gallo St', '1 - A', 'Manapla', 'Philippines', '6120', 'Jocelyn Lim Pacificador', 'Bern Dimakiling Minada', 'Manapla Elementary School', '2019', 'Colegio San Agustin Bacolod', '2025', 1, 6),
(61, 78, 'Kenrich Yver', 'Cenas', 'Nemenzo', '2006-11-13', 'male', '09274044304', 'Binit-agan 1', 'Binit-agan 1', 'Buenavista', 'Escalante City', 'Philippines', '6124', 'Clarife I. Cenas', 'Rey S. Nemenzo', 'Abaga Central Elementary School', '2019', 'Mount Carmel College of Escalante, INC', '2025', 1, 3),
(62, 79, 'Marianne Ysabelle', 'Espina', 'Nabalona', '2006-03-03', 'female', '09206271223', 'Lot 26 & 28', 'Angaw Street., St. Francis Subd.', 'Brgy. V', 'Silay City', 'Philippines', '6116', 'Ma. Milagros Lamayo Espina', 'Rodney L. Nabalona', 'St. Theresitas Academy', '0000', 'Don Bosco Technical Institute Victorias', '0000', 1, 4),
(63, 80, 'Lance Dominic', 'Maravalles', 'Ngitngit', '2007-09-03', 'male', '09939971477', 'N/A', 'Purok Bulaw', 'Brgy. Central Tabao', 'Valladolid', 'Philippines', '6103', 'Emelou S. Maravalles', 'Romeo D, Ngitngit', 'Tabao Elementary School', '2018', 'Tabao National Highschool', '2025', 1, 4),
(64, 81, 'Jayvee', 'Blaco', 'Peña', '2007-06-06', 'male', '09703651484', 'Lot 17 Block 8', 'Grandville 2', 'Mansilingan', 'Bacolod', 'Philippines', '6100', 'Analie Ontalan Blaco', 'Arnel Hallares Peña', 'Jose J. Gonzaga Elementary School', '2019', 'Bacolod City National High School', '2025', 1, 3),
(65, 82, 'Rhyza Marie', 'Hermano', 'Pamañan', '2007-04-15', 'female', '09471561479', 'N/A', 'Bonifacio Extension', '4', 'Silay City', 'Philippines', '6116', 'Ma. Riza B. Hermano', 'Randy D. Pamañan', 'Faith Christian Academy - Silay', '2019', 'Colegio San Agustin - Bacolod', '2025', 1, 2),
(66, 83, 'Justin Nicole', 'Addan', 'Pancho', '2007-11-16', 'female', '09927402059', 'Bago City', 'KM15', 'Barangay calumangan', 'Bago City', 'Philippines', '6101', 'Cyrine Addan', 'Cristituto Pancho', 'Asian Youth Children Ministry Christian School', '2019', 'Ramon Torres National High School', '2023', 1, 4),
(67, 84, 'Dhan Azer', 'Epelipcia', 'Getida', '2006-12-05', 'male', '09817414376', '', 'Purok Mahidaiton', 'Payauan', 'Candoni', 'Philippines', '6110', 'Cela Mae Ramos Epelipcia', 'Anecito Solis Getida', 'Payauan Elementary School', '2019', 'Our Lady our lourdes high School', '2025', 1, 4),
(68, 85, 'Klaui', 'Catuiran', 'Penetrante', '2007-08-06', 'female', '09161733418', '', 'Purok Kasilingan', 'Barangay Tangub', 'Bacolod City', 'Philippines', '6100', 'Ma. Lea G. Catuiran', 'Jason M. Penetrante', 'Education and Training Center School III', '2019', 'Negros Occidental High School', '2025', 1, 2),
(69, 86, 'Aerone Carl', 'Pinasas', 'Penol', '2007-06-12', 'male', '09076858027', '6128', 'Purok 1', 'Brgy. Camp Clark', 'Isabela', 'Philippines', '6128', 'Charona Pinasas', 'Stephen Cyril Penol', 'Camp Clark Elementary School', '2019', 'Isabela National High School', '2025', 4, 1),
(70, 87, 'Renee Gael Alexis', 'Macatangay', 'Perez', '2007-07-09', 'male', '09071469621', 'Block 10, Lot 2, Country Homes Phase 3', 'Estefania Rd.', 'Brgy. Estefania', 'Bacolod', 'Philippines', '6100', 'Lea A. Macatangay', 'Greg A. Perez', 'Quezon Central Elementary School', '2019', 'Palawan State University Laboratory High School', '2025', 1, 2),
(71, 88, 'Anna Kathrina', 'Dulman', 'Roa', '2007-10-12', 'female', '09204819944', 'Lot 3', 'Azucena Street', 'Barangay Estefania', 'Bacolod', 'Philippines', '6100', 'April Dream Velasco Dulman', 'Achilles Barbosa Roa Jr.', 'Patricia Homes Elementary School', '2018', 'Maranatha Christian College', '2022', 1, 6),
(72, 89, 'John Mikhail', 'Datulayta', 'Rojo', '2007-04-13', 'male', '09121505225', 'Block 1 Lot 4', 'Matatag 1', 'Barangay Maao', 'Bago City', 'Philippines', '6101', 'Maria Luisa A. Datulayta', 'Jobert V. Rojo', 'Maao Elementary School', '2019', 'Ramon Torres Louisiana National Highschool', '2023', 1, 2),
(73, 90, 'rovic', 'doque', 'ruales', '2006-11-19', 'male', '09940597110', '', 'hda. dama', 'brgy. mambagaton', 'himamaylan city', 'Philippines', '6108', 'raelyn c. doque', 'roberto y. doque', 'mambagaton elementary school', '2019', 'rafael b. lacson memorial high school', '2025', 1, 2),
(74, 91, 'Sherah', 'Gamayao', 'Salabania', '2006-12-10', 'female', '09687351922', 'lot 11', 'Hope Village', 'San Fernando', 'Talisay City', 'Philippines', '6115', 'Sherlilyn G. Gamayao', 'Marvin T. Salabania', 'Dos Hermanas Elementary School', '2019', 'Rafael B. Lacson Memorial HIgh School', '2025', 1, 3),
(75, 92, 'Josh Crivin', 'Del Sol', 'Rubio', '2006-10-25', 'male', '09937765449', '', 'Purok Tay-tay', 'Mabini', 'Valladolid', 'Philippines', '6103', 'Cristina P. Del Sol', 'Marvin T. Rubio', 'Emilio Infante Elementary School', '2019', 'Tabao National High School', '2025', 1, 3),
(76, 93, 'Jojie Glyn', 'Dequina', 'Salaver', '2005-12-03', 'female', '09060280994', 'Lot 6', 'Purok Himaya', 'Mansilingan', 'Bacolod City', 'Philippines', '6100', 'Ginalyn S. Dequina', 'Jose L. Salaver', 'Educaion and Training Center School II', '2019', 'Negros Occidental High School', '2025', 1, 3),
(77, 94, 'John Michael', 'Tolentino', 'Samar', '2007-06-23', 'male', '09183164305', '', 'Sitio Takas', 'E. B. Magalona', '', 'Philippines', '6118', 'Shella Mae J. Tolentino', 'Eric John C. Samar', 'Rita Lovino Elementary School', '2019', 'Enrique B. Maglona Natiolnal High School', '2025', 1, 2),
(78, 95, 'Marineth', 'Alajito', 'San Jose', '2006-10-31', 'female', '09772515687', '', '', 'Brgy. Matab-ang', 'Talisay', 'Philippines', '6115', 'Roda Alajito', 'Orlando San Jose', 'Don Enrique Lizares Elementary School', '2019', 'Don Hilarion G. Gonzaga Memorial High School', '2023', 1, 2),
(79, 96, 'Scott Haley', 'Cañete', 'Sañor', '2005-08-06', 'male', '09270823470', '1757', 'Saint Mary Street', 'Barangay Villamonte', 'Bacolod City', 'Philippines', '6100', 'Nanlin Cañete', 'Jose Larry L. Sañor', 'St. Scholastica\'s Academy Bacolod', '2018', 'St. Scholastica\'s Academy Bacolod', '2023', 1, 3),
(80, 97, 'Jenesis', 'Flores', 'Savedor', '2006-09-15', 'male', '09274181576', 'lot 10', 'block 28', 'Brgy. Bnnquerohan', 'Cadiz', 'Philippines', '6121', 'Susie Arranguez Flores', 'Jemer Remegio Savedor', 'Cadiz West Elementary School 2', '2019', 'Dr. Vicente F. Gustilo Memorial National High School', '2025', 1, 3),
(81, 98, 'Francis James', 'Ordilla', 'Serdevilla', '2007-02-11', 'male', '09107085743', '', 'Mabini Street', 'Baranggay Zone 8', 'Talisay City', 'Philippines', '', 'Glenda Camero Ordilla', 'Harold Gecangao Serdevilla', 'Talisay Elementary School', '2019', 'Rafael B. Lacson Memorial High School', '2025', 1, 4),
(82, 99, 'DANIELLA MAY', 'FERNANDEZ', 'SICAT', '2007-05-26', 'female', '09948621808', '', 'PUROK ORCHIDS', 'BRGY. BATUAN', 'LA CARLOTA CITY', 'Philippines', '6130', 'DIANA BARRIENTOS SICAT', 'DANTE MOLINES SICAT', 'BATUAN ELEMENTARY SCHOOL', '2018', 'DONA HORTENCIA SALAS BENEDICTO NATIONAL HIGH SCHOOL', '2024', 1, 2),
(83, 100, 'Jose Gabriel', 'Ponce', 'Sucaldito', '2007-03-21', 'male', '09319787796', 'Bacolod City', 'N/A', 'Mansilingan', 'Bacolod', 'Philippines', '6100', 'Niezl G. Ponce', 'Jose Maria K. Sucaldito', 'Shiloh Christian School', '2019', 'Shiloh Christian School', '2023', 1, 3),
(84, 101, 'Franz Denielle', 'Veraye', 'Suela', '2007-10-18', 'female', '09683292865', 'N/A', 'Purok Tinago', 'Barangay Zone 16', 'Talisay City', 'Philippines', '6115', 'Merlinda Y. Veraye', 'Francis T. Suela', 'Efigenio Enrica Lizares Memorial Schol', '2019', 'Rafael B. Lacson Memorial High School', '2025', 1, 4),
(85, 102, 'Sabina Victoria', 'Mendoza', 'Tablazon', '2007-01-05', 'female', '09206305109', 'Blk 26 Lot 16', 'Carmel St.', 'Brgy. Estefania', 'Bacolod City', 'Philippines', '6100', 'Diza G. Mendoza', 'Pablito J. Tablazon', 'Bacolod Christian College of Negros', '2019', 'University of Negros Occidental - Recoletos', '2025', 1, 2),
(86, 103, 'Jillian', 'Villaruel', 'Tauban', '2007-03-26', 'female', '09610709662', '', '', 'Barangay Pahanocoy', 'Bacolod City', 'Philippines', '6100', 'Janet N. Villaruel', 'Miguel R. Tauban', 'Sum-ag Elementary School', '2019', 'Negros Occidental High School', '2025', 1, 6),
(87, 104, 'Brent Louie', 'Moraca', 'Tingson', '2006-11-25', 'male', '09858076958', 'N/A', 'Prk. Sta.Cruz, Brgy. Tagukon, Kabankalan City', 'Tagukon', 'Kabankalan', 'Philippines', '6111', 'Ludelyn C. Moraca', 'Baltazar D. Tingson', 'Tagukon Elementary School', '2019', 'Tagukon National High School', '2023', 1, 12),
(88, 105, 'Jillian', 'Abiday', 'Valdevia', '2007-07-03', 'female', '09157144054', '', 'Rodriguez Street', 'Brgy. 2', 'La Carlota City', 'Philippines', '6130', 'Maria Elena M. Abiday', 'Ronald H. Valdevia', 'La Carlota South Elementary School- 1', '2019', 'Dona Hortencia Salas Benedicto National High School- SHS', '2025', 1, 2),
(89, 106, 'Jheniel', 'Gerona', 'Fernandez', '2006-12-18', 'female', '09637564492', '', '4th Street', 'Brgy. Haguimit', 'La Carlota City', 'Philippines', '6130', 'Gina O. Gerona', 'Nelo J. Fernandez', 'Haguimit Elementary School 1', '2018', 'Dona Hortencia Salas Benedicto National High School- SHS', '2024', 1, 2),
(90, 107, 'Rica', 'Palmer', 'Velez', '2007-01-20', 'female', '09456994541', 'NA', 'Dela Rama st', 'Taculing', 'Bacolod', 'Philippines', '6100', 'Lenie D. Palmer', 'Jose Rey D. Velez', 'Crispino V. Ramos Elementary School', '2019', 'Bacolod City National High School', '2023', 1, 2),
(91, 108, 'Crystel Grace', 'Caparida', 'Vergara', '2007-01-17', 'female', '09815591017', 'Bacolod City', 'Purok Lunok, Bangga Totong 4', 'Brgy. Felisa', 'Bacolod', 'Philippines', '6100', 'Daisy Sandot Caparida', 'Robert Dioquino Vergara Sr.', 'Felisa Elementary School', '0000', 'Handumanan National High School', '0000', 1, 3),
(92, 109, 'Gerald', 'Gaurino', 'Vingco', '2007-04-25', 'male', '09919077737', 'N/A', 'Purok Greenshell', 'Barangay Zone 3', 'Talisay City', 'Philippines', '6115', 'Rhea Mae Gaurino', 'Gilbert Asin Vingco', 'Talisay South Elementary School', '2019', 'Inocensio V. Ferrer Memorial School of Fisheries', '2025', 4, 6),
(93, 110, 'Rhean Larene', 'Calvez', 'Treyes', '2007-05-22', 'female', '09942497258', 'Bacolod City', 'Diamond Street', 'Brgy. Mansilingan', 'Bacolod', 'Philippines', '6100', 'Jenylene Juadiong Calvez', 'Rio Polvorido Treyes', 'Education And Training Center School 2', '2018', 'Negros Occidental High School', '2025', 1, 3),
(94, 111, 'Raziah Lee', 'Racelis', 'Torres', '2007-09-29', 'female', '09300283883', '', 'Purok Malapitan', 'Brgy. Alijis', 'Bacolod', 'Philippines', '6100', 'Joana Torres', 'Roderick Torres', 'Emilia J. Garcia Elementary School', '2018', 'Maranatah Christian Academy', '2023', 1, 8);

-- applications
INSERT INTO `applications` (`application_id`, `user_id`, `program_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 17, 1, 'pending', '2025-05-05 18:30:29', '2025-06-10 00:33:11'),
(3, 20, 1, 'pending', '2025-05-05 21:31:59', '2025-05-05 21:31:59'),
(4, 19, 1, 'pending', '2025-05-05 21:33:04', '2025-05-05 21:33:04'),
(5, 21, 1, 'pending', '2025-05-05 21:39:13', '2025-05-05 21:39:13'),
(6, 22, 1, 'pending', '2025-05-05 21:45:27', '2025-05-05 21:45:27'),
(7, 23, 1, 'pending', '2025-05-06 16:31:42', '2025-05-06 16:31:42'),
(8, 24, 1, 'pending', '2025-05-06 16:38:06', '2025-05-06 16:38:06'),
(9, 25, 1, 'pending', '2025-05-06 16:44:32', '2025-05-06 16:44:32'),
(10, 26, 1, 'pending', '2025-05-06 16:54:18', '2025-05-06 16:54:18'),
(11, 27, 1, 'pending', '2025-05-06 21:37:07', '2025-05-06 21:37:07'),
(12, 28, 1, 'pending', '2025-05-06 21:49:10', '2025-05-06 21:49:10'),
(13, 29, 1, 'pending', '2025-05-06 21:57:49', '2025-05-06 21:57:49'),
(14, 30, 1, 'pending', '2025-05-06 22:06:28', '2025-05-06 22:06:28'),
(15, 31, 1, 'pending', '2025-05-06 22:22:50', '2025-05-06 22:22:50'),
(16, 32, 1, 'pending', '2025-05-07 16:35:17', '2025-05-07 16:35:17'),
(17, 33, 1, 'pending', '2025-05-07 16:43:33', '2025-05-07 16:43:33'),
(18, 34, 1, 'pending', '2025-05-07 16:52:46', '2025-05-07 16:52:46'),
(19, 40, 1, 'pending', '2025-05-07 21:42:00', '2025-05-07 21:42:00'),
(20, 41, 1, 'pending', '2025-05-07 21:51:55', '2025-05-07 21:51:55'),
(21, 36, 1, 'pending', '2025-05-07 21:54:22', '2025-05-07 21:54:22'),
(22, 42, 1, 'pending', '2025-05-07 21:59:07', '2025-05-07 21:59:07'),
(23, 43, 1, 'pending', '2025-05-07 22:05:04', '2025-05-07 22:05:04'),
(24, 45, 1, 'pending', '2025-05-08 16:35:17', '2025-05-08 16:35:17'),
(25, 46, 1, 'pending', '2025-05-08 16:41:56', '2025-05-08 16:41:56'),
(26, 47, 1, 'pending', '2025-05-08 16:49:58', '2025-05-08 16:49:58'),
(27, 48, 1, 'pending', '2025-05-08 17:05:49', '2025-05-08 17:05:49'),
(28, 44, 1, 'pending', '2025-05-08 17:14:46', '2025-05-08 17:14:46'),
(29, 55, 1, 'pending', '2025-05-12 17:00:42', '2025-05-12 17:00:42'),
(30, 56, 1, 'pending', '2025-05-12 17:07:48', '2025-05-12 17:07:48'),
(31, 57, 1, 'pending', '2025-05-12 17:16:03', '2025-05-12 17:16:03'),
(32, 58, 1, 'pending', '2025-05-12 21:35:40', '2025-05-12 21:35:40'),
(33, 59, 1, 'pending', '2025-05-12 21:41:12', '2025-05-12 21:41:12'),
(34, 60, 1, 'pending', '2025-05-12 21:48:55', '2025-05-12 21:48:55'),
(35, 61, 1, 'pending', '2025-05-12 21:56:24', '2025-05-12 21:56:24'),
(36, 62, 1, 'pending', '2025-05-13 16:46:36', '2025-05-13 16:46:36'),
(37, 63, 1, 'pending', '2025-05-13 16:53:10', '2025-05-13 16:53:10'),
(38, 65, 1, 'pending', '2025-05-13 21:36:38', '2025-05-13 21:36:38'),
(39, 66, 1, 'pending', '2025-05-13 21:44:32', '2025-05-13 21:44:32'),
(40, 67, 1, 'pending', '2025-05-13 21:50:56', '2025-05-13 21:50:56'),
(41, 68, 1, 'pending', '2025-05-13 21:59:28', '2025-05-13 21:59:28'),
(42, 69, 1, 'pending', '2025-05-13 22:06:04', '2025-05-13 22:06:04'),
(43, 70, 1, 'pending', '2025-05-14 16:42:44', '2025-05-14 16:42:44'),
(44, 71, 1, 'pending', '2025-05-14 17:07:12', '2025-05-14 17:07:12'),
(45, 72, 1, 'pending', '2025-05-14 17:49:00', '2025-05-14 17:49:00'),
(46, 73, 1, 'pending', '2025-05-14 18:14:28', '2025-05-14 18:14:28'),
(47, 74, 1, 'pending', '2025-05-14 21:38:58', '2025-05-14 21:38:58'),
(48, 75, 1, 'pending', '2025-05-14 22:15:20', '2025-05-14 22:15:20'),
(49, 77, 1, 'pending', '2025-05-14 22:16:10', '2025-05-14 22:16:10'),
(50, 76, 1, 'pending', '2025-05-14 22:17:00', '2025-05-14 22:17:00'),
(51, 79, 1, 'pending', '2025-05-15 16:49:14', '2025-05-15 16:49:14'),
(52, 81, 1, 'pending', '2025-05-18 16:50:09', '2025-05-18 16:50:09'),
(53, 82, 1, 'pending', '2025-05-18 16:57:52', '2025-05-18 16:57:52'),
(54, 84, 1, 'pending', '2025-05-18 17:14:51', '2025-05-18 17:14:51'),
(55, 85, 1, 'pending', '2025-05-18 21:30:18', '2025-05-18 21:30:18'),
(56, 86, 1, 'pending', '2025-05-18 21:46:42', '2025-05-18 21:46:42'),
(57, 88, 1, 'pending', '2025-05-19 16:43:58', '2025-05-19 16:43:58'),
(58, 89, 1, 'pending', '2025-05-19 16:56:11', '2025-05-19 16:56:11'),
(59, 90, 1, 'pending', '2025-05-19 17:06:25', '2025-05-19 17:06:25'),
(60, 91, 1, 'pending', '2025-05-19 17:14:13', '2025-05-19 17:14:13'),
(61, 92, 1, 'pending', '2025-05-19 17:20:23', '2025-05-19 17:20:23'),
(62, 93, 1, 'pending', '2025-05-19 21:36:11', '2025-05-19 21:36:11'),
(63, 98, 1, 'pending', '2025-05-20 16:38:34', '2025-05-20 16:38:34'),
(64, 99, 1, 'pending', '2025-05-20 16:48:40', '2025-05-20 16:48:40'),
(65, 100, 1, 'pending', '2025-05-20 21:42:16', '2025-05-20 21:42:16'),
(66, 101, 1, 'pending', '2025-05-20 21:47:51', '2025-05-20 21:47:51'),
(67, 102, 1, 'pending', '2025-05-20 21:52:34', '2025-05-20 21:52:34'),
(68, 103, 1, 'pending', '2025-05-20 22:03:58', '2025-05-20 22:03:58'),
(69, 105, 1, 'pending', '2025-05-21 16:40:17', '2025-05-21 16:40:17'),
(70, 106, 1, 'pending', '2025-05-21 16:47:32', '2025-05-21 16:47:32'),
(71, 107, 1, 'pending', '2025-05-21 16:54:38', '2025-05-21 16:54:38'),
(72, 108, 1, 'pending', '2025-05-21 17:02:01', '2025-05-21 17:02:01'),
(73, 110, 1, 'pending', '2025-05-21 17:18:03', '2025-05-21 17:18:03'),
(74, 114, 1, 'pending', '2025-05-21 18:56:31', '2025-05-21 18:56:31'),
(75, 115, 1, 'pending', '2025-05-22 18:55:51', '2025-05-22 18:55:51'),
(76, 116, 8, 'pending', '2025-05-22 19:31:26', '2025-05-22 19:31:26'),
(77, 120, 2, 'pending', '2025-05-29 22:00:40', '2025-05-29 22:00:40'),
(78, 124, 9, 'pending', '2025-06-10 20:41:44', '2025-06-10 20:41:44');

-- colleges
INSERT INTO `colleges` (`college_id`, `college_name`, `college_code`, `description`, `created_at`, `updated_at`) VALUES
-- ... (all values from the dump) ...

-- exam_registrations
INSERT INTO `exam_registrations` (`registration_id`, `applicant_id`, `exam_schedule_id`, `registration_date`, `status`) VALUES
-- ... (all values from the dump) ...

-- exam_schedules
INSERT INTO `exam_schedules` (`exam_id`, `exam_date`, `exam_time`, `venue`, `max_participants`, `status`, `created_at`, `updated_at`) VALUES
-- ... (all values from the dump) ...

-- exam_scores
INSERT INTO `exam_scores` (`score_id`, `registration_id`, `score`, `rank`, `assigned_program_id`, `interview_schedule_id`, `status`, `created_at`, `updated_at`) VALUES
-- ... (all values from the dump) ...

-- interviews
INSERT INTO `interviews` (`interview_id`, `application_id`, `interviewer_id`, `program_head_id`, `scheduled_date`, `scheduled_time`, `status`, `result`, `score`, `notes`, `completed_date`, `created_at`, `updated_at`) VALUES
-- ... (all values from the dump) ...

-- interview_schedules
INSERT INTO `interview_schedules` (`schedule_id`, `program_id`, `interview_date`, `time_window`, `max_applicants`, `current_applicants`, `status`, `created_by`, `created_at`) VALUES
-- ... (all values from the dump) ...

-- notifications
INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
-- ... (all values from the dump) ...

-- programs
INSERT INTO `programs` (`program_id`, `college_id`, `program_head_id`, `program_name`, `program_code`, `description`, `created_at`, `updated_at`) VALUES
-- ... (all values from the dump) ...

-- program_cutoffs
INSERT INTO `program_cutoffs` (`cutoff_id`, `program_id`, `start_rank`, `end_rank`, `is_active`, `created_at`, `updated_at`) VALUES
-- ... (all values from the dump) ...

-- program_heads
INSERT INTO `program_heads` (`program_head_id`, `user_id`, `program_id`, `status`, `created_at`, `updated_at`) VALUES
-- ... (all values from the dump) ...

-- program_rankings
INSERT INTO `program_rankings` (`ranking_id`, `applicant_id`, `program_id`, `exam_score`, `rank_position`, `is_eligible`, `assigned_program_id`, `created_at`, `updated_at`) VALUES
-- ... (all values from the dump) ...

-- users
INSERT INTO `users` (`user_id`, `username`, `password`, `first_name`, `last_name`, `email`, `phone`, `user_type`, `status`, `created_at`, `updated_at`) VALUES
-- ... (all values from the dump) ...

-- End of data-only dump 