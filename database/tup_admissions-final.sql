  -- phpMyAdmin SQL Dump
  -- version 5.2.1
  -- https://www.phpmyadmin.net/
  --
  -- Host: 127.0.0.1
  -- Generation Time: Jun 04, 2025 at 04:31 PM
  -- Server version: 10.4.32-MariaDB
  -- PHP Version: 8.0.30

  SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
  START TRANSACTION;
  SET time_zone = "+00:00";


  /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
  /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
  /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
  /*!40101 SET NAMES utf8mb4 */;

  --
  -- Database: `tup_admissions`
  --

  -- --------------------------------------------------------

  --
  -- Table structure for table `activity_log`
  --

  CREATE TABLE `activity_log` (
    `log_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `action` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `activity_log`
  --

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
  (19, 1, 'Marked exam schedule ID: 4 as completed', '2025-06-02 16:29:17');

  -- --------------------------------------------------------

  --
  -- Table structure for table `applicants`
  --

  CREATE TABLE `applicants` (
    `applicant_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `first_name` varchar(50) NOT NULL,
    `middle_name` varchar(50) DEFAULT NULL,
    `last_name` varchar(50) NOT NULL,
    `birth_date` date NOT NULL,
    `gender` enum('male','female','other') NOT NULL,
    `mobile_number` varchar(20) NOT NULL,
    `address_lot` varchar(50) DEFAULT NULL,
    `address_street` varchar(100) DEFAULT NULL,
    `address_town` varchar(100) DEFAULT NULL,
    `address_city` varchar(100) DEFAULT NULL,
    `address_country` varchar(100) DEFAULT 'Philippines',
    `address_zipcode` varchar(10) DEFAULT NULL,
    `mother_maiden_name` varchar(100) DEFAULT NULL,
    `father_name` varchar(100) DEFAULT NULL,
    `elementary_school` varchar(100) NOT NULL,
    `elementary_year_graduated` year(4) NOT NULL,
    `high_school` varchar(100) NOT NULL,
    `high_school_year_graduated` year(4) NOT NULL,
    `primary_program_id` int(11) DEFAULT NULL,
    `secondary_program_id` int(11) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `applicants`
  --


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
  (94, 111, 'Raziah Lee', 'Racelis', 'Torres', '2007-09-29', 'female', '09300283883', '', 'Purok Malapitan', 'Brgy. Alijis', 'Bacolod', 'Philippines', '6100', 'Joana Torres', 'Roderick Torres', 'Emilia J. Garcia Elementary School', '2018', 'Maranatah Christian Academy', '2023', 1, 8),
  (95, 114, 'Alex', 'T', 'Diams', '2025-05-06', 'male', '12345', '1234', '1234', '1234', '122343', 'Philippines', '1234', 'Mama', 'Papa', 'ETCS', '2000', 'BCNHS', '2004', 1, 2),
  (96, 115, 'Alex', 'Torres', 'Dy', '2025-05-18', 'female', '12345', '12345', '12345', '12345', 'Bacolod', 'Philippines', '6100', 'Dfgh', 'Errfg', 'Drf', '0000', 'Dffvuyt', '0000', 1, 8),
  (97, 116, 'Aleks', 'Tee', 'Dia', '2025-05-31', 'male', '45678', 'Rtghh', 'Gguh', 'Fghb', 'Bacolod', 'Philippines', '6100', 'Fggh', 'Ttyh', 'Fgg', '0000', 'Tyy', '0000', 8, 1),
  (98, 118, '', '', 'dasd', '0000-00-00', 'male', '', '', '', '', '', 'Philippines', '', '', '', 'La Carlota South Elementary School- 1', '2019', 'Dona Hortencia Salas Benedicto National High School- SHS', '2020', 1, 8),
  (99, 119, 'Alop', 'dsd', 'dsdv', '2025-05-21', 'male', '4575', 'na', 'na', 'ad', 'gfgg', 'Philippines', '6100', 'asd', 'ass', '22', '0000', '1224', '0000', 1, 14),
  (100, 120, 'Aleks', '', 'Diams', '0000-00-00', 'male', '', '', '', '', '', 'Philippines', '', '', '', '', '0000', '', '0000', 2, 1),
  (101, 121, 'Aleeks', '', 'Diams', '0000-00-00', 'male', '', '', '', '', '', 'Philippines', '', '', '', '', '0000', '', '0000', 1, 4),
  (102, 122, 'Aleeeks', '', 'Diams', '0000-00-00', 'female', '', '', '', '', '', 'Philippines', '', '', '', '', '0000', '', '0000', 4, 2),
  (103, 123, 'Aleeeeks', '', 'Diamants', '0000-00-00', 'male', '', '', '', '', '', 'Philippines', '', '', '', '', '0000', '', '0000', 2, 3);

  -- --------------------------------------------------------

  --
  -- Table structure for table `applications`
  --

  CREATE TABLE `applications` (
    `application_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `program_id` int(11) NOT NULL,
    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `applications`
  --

  INSERT INTO `applications` (`application_id`, `user_id`, `program_id`, `status`, `created_at`, `updated_at`) VALUES
  (1, 2, 1, 'pending', '2025-05-30 13:31:58', '2025-05-30 13:31:58'),
  (2, 17, 1, 'approved', '2025-06-02 04:33:12', '2025-06-02 16:37:24'),
  (3, 31, 1, 'pending', '2025-06-02 15:24:55', '2025-06-02 15:24:55'),
  (4, 38, 1, 'approved', '2025-06-02 16:09:59', '2025-06-02 16:37:42');

  -- --------------------------------------------------------

  --
  -- Table structure for table `colleges`
  --

  CREATE TABLE `colleges` (
    `college_id` int(11) NOT NULL,
    `college_name` varchar(100) NOT NULL,
    `college_code` varchar(20) NOT NULL,
    `description` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `colleges`
  --

  INSERT INTO `colleges` (`college_id`, `college_name`, `college_code`, `description`, `created_at`, `updated_at`) VALUES
  (1, 'College of Engineering', 'COE', 'College of Engineering offers various engineering programs', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (2, 'College of Automation and Control', 'CAC', 'College of Automation and Control focuses on automation and control systems', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (3, 'College of Engineering Technology', 'CET', 'College of Engineering Technology provides technical education', '2025-05-30 13:28:23', '2025-05-30 13:28:23');

  -- --------------------------------------------------------

  --
  -- Table structure for table `exam_registrations`
  --

  CREATE TABLE `exam_registrations` (
    `registration_id` int(11) NOT NULL,
    `applicant_id` int(11) NOT NULL,
    `exam_schedule_id` int(11) NOT NULL,
    `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
    `status` enum('registered','cancelled','completed') NOT NULL DEFAULT 'registered'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `exam_registrations`
  --

  INSERT INTO `exam_registrations` (`registration_id`, `applicant_id`, `exam_schedule_id`, `registration_date`, `status`) VALUES
  (7, 77, 4, '2025-06-02 16:25:58', 'completed');

  -- --------------------------------------------------------

  --
  -- Table structure for table `exam_results`
  --

  CREATE TABLE `exam_results` (
    `result_id` int(11) NOT NULL,
    `exam_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `score` decimal(5,2) NOT NULL,
    `status` enum('passed','failed') NOT NULL,
    `notes` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `exam_results`
  --

  INSERT INTO `exam_results` (`result_id`, `exam_id`, `user_id`, `score`, `status`, `notes`, `created_at`, `updated_at`) VALUES
  (1, 2, 2, 80.00, 'passed', NULL, '2025-05-30 15:44:00', '2025-06-02 16:16:53'),
  (38, 4, 38, 12.00, 'failed', NULL, '2025-06-02 16:49:47', '2025-06-02 16:49:55');

  -- --------------------------------------------------------

  --
  -- Table structure for table `exam_schedules`
  --

  CREATE TABLE `exam_schedules` (
    `exam_id` int(11) NOT NULL,
    `exam_date` date NOT NULL,
    `exam_time` time NOT NULL,
    `venue` varchar(255) NOT NULL,
    `max_participants` int(11) NOT NULL,
    `status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `exam_schedules`
  --

  INSERT INTO `exam_schedules` (`exam_id`, `exam_date`, `exam_time`, `venue`, `max_participants`, `status`, `created_at`, `updated_at`) VALUES
  (1, '2025-05-30', '08:00:00', 'TUP Visayas', 20, 'scheduled', '2025-05-30 13:56:53', '2025-06-02 16:25:35'),
  (2, '2025-06-02', '08:00:00', 'TUP Visayas', 20, 'scheduled', '2025-05-30 13:57:28', '2025-06-02 16:25:43'),
  (3, '2025-06-09', '08:00:00', 'Erams East', 50, 'cancelled', '2025-06-02 15:23:50', '2025-06-02 16:13:02'),
  (4, '2025-06-10', '07:00:00', 'TUP VISAYAS', 50, 'completed', '2025-06-02 16:24:41', '2025-06-02 16:29:17');

  -- --------------------------------------------------------

  --
  -- Table structure for table `exam_scores`
  --

  CREATE TABLE `exam_scores` (
    `score_id` int(11) NOT NULL,
    `registration_id` int(11) NOT NULL,
    `score` decimal(5,2) NOT NULL,
    `rank` int(11) DEFAULT NULL,
    `assigned_program_id` int(11) DEFAULT NULL,
    `interview_schedule_id` int(11) DEFAULT NULL,
    `status` enum('pending','qualified','not_qualified') NOT NULL DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  -- --------------------------------------------------------

  --
  -- Table structure for table `interviewers`
  --

  CREATE TABLE `interviewers` (
    `user_id` int(11) NOT NULL,
    `program_id` int(11) DEFAULT NULL,
    `qualifications` text NOT NULL,
    `experience` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  -- --------------------------------------------------------

  --
  -- Table structure for table `interviews`
  --

  CREATE TABLE `interviews` (
    `interview_id` int(11) NOT NULL,
    `application_id` int(11) NOT NULL,
    `interviewer_id` int(11) NOT NULL,
    `program_head_id` int(11) NOT NULL,
    `scheduled_date` date NOT NULL,
    `scheduled_time` time NOT NULL,
    `status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
    `result` enum('passed','failed') DEFAULT NULL,
    `score` decimal(5,2) DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `completed_date` datetime DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `interviews`
  --

  INSERT INTO `interviews` (`interview_id`, `application_id`, `interviewer_id`, `program_head_id`, `scheduled_date`, `scheduled_time`, `status`, `result`, `score`, `notes`, `completed_date`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, 1, '2024-04-15', '00:00:00', 'completed', 'passed', 25.00, 'Evaluation Scores:\nInterest and Motivation: 5/5\nCommunication Skills: 5/5\nComprehension and Critical Thinking: 5/5\nProgram-Relevant Skills: 5/5\nFinancial Capacity: 5/5\nTotal Score: 25/25\n\nAdditional Notes:\nasdasdas', '2025-05-30 21:32:35', '2025-05-30 13:31:59', '2025-05-30 13:32:35'),
  (2, 2, 1, 1, '2024-04-15', '00:00:00', 'completed', 'passed', 19.00, 'Evaluation Scores:\nInterest and Motivation: 5/5\nCommunication Skills: 5/5\nComprehension and Critical Thinking: 5/5\nProgram-Relevant Skills: 3/5\nFinancial Capacity: 1/5\nTotal Score: 19/25\n\nAdditional Notes:\n', '2025-06-02 23:44:49', '2025-06-02 04:33:12', '2025-06-02 15:44:49'),
  (3, 3, 1, 1, '2024-04-15', '00:00:00', 'completed', 'passed', 24.00, 'Evaluation Scores:\nInterest and Motivation: 4/5\nCommunication Skills: 5/5\nComprehension and Critical Thinking: 5/5\nProgram-Relevant Skills: 5/5\nFinancial Capacity: 5/5\nTotal Score: 24/25\n\nAdditional Notes:\n', '2025-06-02 23:41:48', '2025-06-02 15:24:55', '2025-06-02 15:41:48'),
  (4, 4, 1, 1, '2024-04-15', '00:00:00', 'scheduled', NULL, NULL, NULL, NULL, '2025-06-02 16:09:59', '2025-06-02 16:09:59');

  -- --------------------------------------------------------

  --
  -- Table structure for table `interview_results`
  --

  CREATE TABLE `interview_results` (
    `result_id` int(11) NOT NULL,
    `applicant_id` int(11) DEFAULT NULL,
    `interview_schedule_id` int(11) DEFAULT NULL,
    `interviewer_id` int(11) DEFAULT NULL,
    `rating` decimal(5,2) NOT NULL,
    `remarks` text DEFAULT NULL,
    `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  -- --------------------------------------------------------

  --
  -- Table structure for table `interview_schedules`
  --

  CREATE TABLE `interview_schedules` (
    `schedule_id` int(11) NOT NULL,
    `program_id` int(11) DEFAULT NULL,
    `interview_date` date NOT NULL,
    `time_window` enum('AM','PM') NOT NULL,
    `max_applicants` int(11) NOT NULL,
    `current_applicants` int(11) DEFAULT 0,
    `status` enum('open','closed','completed') DEFAULT 'open',
    `created_by` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `interview_schedules`
  --

  INSERT INTO `interview_schedules` (`schedule_id`, `program_id`, `interview_date`, `time_window`, `max_applicants`, `current_applicants`, `status`, `created_by`, `created_at`) VALUES
  (1, 1, '2024-04-15', 'AM', 20, 4, 'open', 1, '2025-05-30 13:28:23'),
  (2, 2, '2024-04-15', 'PM', 20, 0, 'open', 1, '2025-05-30 13:28:23');

  -- --------------------------------------------------------

  --
  -- Table structure for table `notifications`
  --

  CREATE TABLE `notifications` (
    `notification_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `title` varchar(100) NOT NULL,
    `message` text NOT NULL,
    `type` enum('exam','interview','result','general') NOT NULL,
    `is_read` tinyint(1) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `notifications`
  --

  INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
  (1, 1, 'New Interview Scheduled', 'New interview scheduled with Rhemjohn Pitong for Bachelor of Science in Electronics Engineering on April 15, 2024 AM', 'interview', 0, '2025-05-30 13:31:59'),
  (2, 1, 'New Interview Scheduled', 'New interview scheduled with   for Bachelor of Science in Electronics Engineering on April 15, 2024 AM', 'interview', 0, '2025-06-02 04:33:12'),
  (3, 1, 'New Interview Scheduled', 'New interview scheduled with   for Bachelor of Science in Electronics Engineering on April 15, 2024 AM', 'interview', 0, '2025-06-02 15:24:55'),
  (4, 1, 'New Interview Scheduled', 'New interview scheduled with   for Bachelor of Science in Electronics Engineering on April 15, 2024 AM', 'interview', 0, '2025-06-02 16:09:59');

  -- --------------------------------------------------------

  --
  -- Table structure for table `programs`
  --

  CREATE TABLE `programs` (
    `program_id` int(11) NOT NULL,
    `college_id` int(11) NOT NULL,
    `program_head_id` int(11) DEFAULT NULL,
    `program_name` varchar(100) NOT NULL,
    `program_code` varchar(20) NOT NULL,
    `description` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `programs`
  --

  INSERT INTO `programs` (`program_id`, `college_id`, `program_head_id`, `program_name`, `program_code`, `description`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, 'Bachelor of Science in Electronics Engineering', 'BS ECE', 'Electronics Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (2, 1, 2, 'Bachelor of Science in Mechanical Engineering', 'BS ME', 'Mechanical Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (3, 1, 3, 'Bachelor of Science in Computer Engineering', 'BS CPE', 'Computer Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (4, 1, 4, 'Bachelor of Science in Electrical Engineering', 'BS EE', 'Electrical Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (5, 2, 5, 'Bachelor of Science in Instrumentation and Control Engineering Technology', 'BS ICET', 'Instrumentation and Control Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (6, 2, 6, 'Bachelor of Science in Mechatronics Engineering', 'BS MxE', 'Mechatronics Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (7, 2, 7, 'Bachelor of Science in Mechatronics Engineering Technology', 'BS MxT', 'Mechatronics Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (8, 3, 8, 'Bachelor of Science in Chemistry', 'BS Chem', 'Chemistry program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (9, 3, 9, 'Bachelor of Engineering major in Chemical Engineering Technology', 'BE ChET', 'Chemical Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (10, 3, 10, 'Bachelor of Science in Automotive Engineering', 'BS AutoE', 'Automotive Engineering program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (11, 3, 11, 'Bachelor of Science in Computer Engineering Technology', 'BS CpET', 'Computer Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (12, 3, 12, 'Bachelor of Science in Electrical Engineering Technology', 'BS EET', 'Electrical Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (13, 3, 13, 'Bachelor of Science in Electromechanical Engineering Technology', 'BS EMET', 'Electromechanical Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (14, 3, 14, 'Bachelor of Science in Heating, Ventilation and Air Conditioning-Refrigeration Engineering Technolog', 'BS HVAC-RET', 'HVAC-RET program', '2025-05-30 13:28:23', '2025-05-30 13:28:24'),
  (15, 3, 15, 'Bachelor of Science in Manufacturing Engineering Technology', 'BS MFET', 'Manufacturing Engineering Technology program', '2025-05-30 13:28:23', '2025-05-30 13:28:23');

  -- --------------------------------------------------------

  --
  -- Table structure for table `program_cutoffs`
  --

  CREATE TABLE `program_cutoffs` (
    `cutoff_id` int(11) NOT NULL,
    `program_id` int(11) NOT NULL,
    `cutoff_rank` int(11) NOT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `program_cutoffs`
  --

  INSERT INTO `program_cutoffs` (`cutoff_id`, `program_id`, `cutoff_rank`, `is_active`, `created_at`, `updated_at`) VALUES
  (1, 9, 500, 1, '2025-05-30 16:07:50', '2025-05-30 16:20:10'),
  (2, 1, 150, 1, '2025-05-30 16:07:58', '2025-05-30 16:07:58'),
  (3, 2, 300, 1, '2025-05-30 16:09:36', '2025-05-30 16:09:36');

  -- --------------------------------------------------------

  --
  -- Table structure for table `program_heads`
  --

  CREATE TABLE `program_heads` (
    `program_head_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `program_id` int(11) NOT NULL,
    `status` enum('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `program_heads`
  --

  INSERT INTO `program_heads` (`program_head_id`, `user_id`, `program_id`, `status`, `created_at`, `updated_at`) VALUES
  (1, 3, 1, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (2, 4, 2, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (3, 5, 3, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (4, 6, 4, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (5, 7, 5, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (6, 8, 6, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (7, 9, 7, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (8, 10, 8, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (9, 11, 9, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (10, 12, 10, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (11, 13, 11, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (12, 14, 12, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (13, 15, 13, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (14, 16, 14, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (15, 17, 15, 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23');
  -- --------------------------------------------------------

  --
  -- Table structure for table `program_rankings`
  --

  CREATE TABLE `program_rankings` (
    `ranking_id` int(11) NOT NULL,
    `applicant_id` int(11) NOT NULL,
    `program_id` int(11) NOT NULL,
    `exam_score` decimal(5,2) NOT NULL,
    `rank_position` int(11) NOT NULL,
    `is_eligible` tinyint(1) DEFAULT 0,
    `assigned_program_id` int(11) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `program_rankings`
  --

  INSERT INTO `program_rankings` (`ranking_id`, `applicant_id`, `program_id`, `exam_score`, `rank_position`, `is_eligible`, `assigned_program_id`, `created_at`, `updated_at`) VALUES
  (1, 1, 1, 80.00, 1, 1, 1, '2025-06-02 16:49:56', '2025-06-02 16:49:56'),
  (2, 77, 1, 12.00, 2, 1, 13, '2025-06-02 16:49:56', '2025-06-02 16:49:56'),
  (4, 1, 2, 80.00, 1, 1, 1, '2025-06-02 16:49:56', '2025-06-02 16:49:56'),
  (5, 77, 13, 12.00, 1, 1, 13, '2025-06-02 16:49:56', '2025-06-02 16:49:56');

  -- --------------------------------------------------------

  --
  -- Table structure for table `users`
  --

  CREATE TABLE `users` (
    `user_id` int(11) NOT NULL,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `first_name` varchar(50) NOT NULL,
    `last_name` varchar(50) NOT NULL,
    `email` varchar(100) NOT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `user_type` enum('admin','interviewer','applicant') NOT NULL,
    `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `users`
  --

  
  INSERT INTO `users` (`user_id`, `username`, `password`, `first_name`, `last_name`, `email`, `phone`, `user_type`, `status`, `created_at`, `updated_at`) VALUES
  (1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', '', 'admin@tup.edu.ph', NULL, 'admin', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (2, 'applicant1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rhemjohn', 'Pitong', 'applicant1@example.com', NULL, 'applicant', 'active', '2025-05-30 13:28:23', '2025-05-30 13:30:01'),
  (3, 'ph_ece', '$2y$10$bgGTgQRuLq1YBohm8VOF..gwLyuVuVlKGA//y0c0/PA920g5orvjG', 'Renato', 'Deldo', 'ph_ece@tup.edu.ph', '', 'interviewer', 'active', '2025-05-30 13:28:23', '2025-06-02 05:55:08'),
  (4, 'ph_me', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Doe', 'ph_me@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (5, 'ph_cpe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Robert', 'Johnson', 'ph_cpe@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (6, 'ph_ee', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Williams', 'ph_ee@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (7, 'ph_icet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Michael', 'Brown', 'ph_icet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (8, 'ph_mxe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emily', 'Davis', 'ph_met@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (9, 'ph_mxt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emily', 'Davis', 'ph_met@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (10, 'ph_chem', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Wilson', 'ph_chem@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (11, 'ph_chet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patricia', 'Taylor', 'ph_chet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (12, 'ph_auto', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'James', 'Anderson', 'ph_auto@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (13, 'ph_cpet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jennifer', 'Thomas', 'ph_cpet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (14, 'ph_eet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Richard', 'Jackson', 'ph_eet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (15, 'ph_emet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Susan', 'White', 'ph_emet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (16, 'ph_hvac', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Charles', 'Harris', 'ph_hvac@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (17, 'ph_mfet', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Margaret', 'Martin', 'ph_mfet@tup.edu.ph', NULL, 'interviewer', 'active', '2025-05-30 13:28:23', '2025-05-30 13:28:23'),
  (18, 'rhemjohn', '$2y$10$/t7ajDgS8E9zYqdv7c.ESutX6AMNXDgCrrDodpGWn3AnIyAEL.Fmu', '', '', 'rdpitong@gmail.com', NULL, 'applicant', 'active', '2025-06-02 04:32:38', '2025-06-02 04:32:59'),
  (19, 'benignosmitz', '$2y$10$1Mn41hSp9pbgDKQwZ7q17uUuO/UNrAvs4obWt4A8Jq95cnGFRiMpW', '', '', 'benignosmitz@gmail.com', NULL, 'applicant', 'active', '2025-05-06 05:25:10', '2025-05-06 05:25:27'),
  (20, 'YPhrog', '$2y$10$R0w4Sv8NwRm9g6O4J01HVu9bnHdj3F9XtrOAx6IBgk74wXSbjLD9W', '', '', 'miggy.b213@gmail.com', NULL, 'applicant', 'active', '2025-05-06 05:31:05', '2025-05-06 05:31:46'),
  (21, 'mikylla biatin', '$2y$10$dMNQe.j.CnvFQKcqmDbjk.wgrsc/K210xQYRneKyXmvFZQIbwMVsO', '', '', 'mikyllabiatin@gmail.com', NULL, 'applicant', 'active', '2025-05-06 05:38:33', '2025-05-06 05:38:55'),
  (22, 'carlzbrizuela@gmail.com', '$2y$10$qbl7qPlhxyiIOoJkQLlAHuLcIR8kkO8g5468cHJdXzMBmaIGuZqLK', '', '', 'carlzbrizuela@gmail.com', NULL, 'applicant', 'active', '2025-05-06 05:44:51', '2025-05-06 05:44:58'),
  (23, 'ANNE QUESIA CALDERON', '$2y$10$JYWPIhQ.Zl.FbvY8NQnIfe57iiEa8HkBjSWrhol7X4r6UxVD/I.D6', '', '', 'calderonannequesia@gmail.com', NULL, 'applicant', 'active', '2025-05-07 00:31:04', '2025-05-07 00:31:16'),
  (24, 'czarenblcanlog', '$2y$10$fzmbdgaQusVj7eUygJalG.ki7pVCswQ9ZI2oywThiBgj65Nen3Os2', '', '', 'czarenblcanlog@gmail.com', NULL, 'applicant', 'active', '2025-05-07 00:37:23', '2025-05-07 00:37:44'),
  (25, 'aldrich', '$2y$10$.zzRivcl3F.wjHaaKciLLutkHfNvXWGUAKtbrUhGamPTZQNfHHIgq', '', '', 'cabrasjohnaldrich7@gmail.com', NULL, 'applicant', 'active', '2025-05-07 00:43:51', '2025-05-07 00:44:02'),
  (26, 'chillguy', '$2y$10$R7fJTuIxWDhFb07w3i/FCu7rg60cRR99unA89Q7a/mtqzIFdi06Zq', '', '', 'kennbyapurado830@gmail.com', NULL, 'applicant', 'active', '2025-05-07 00:53:41', '2025-05-07 00:53:51'),
  (27, 'angelacapunong', '$2y$10$XBm1KxY5ppk9gJmtxAdIQuWVkah5emyznPvwyKD1cs.AI5qlHpjtW', '', '', 'angelacapunong1007@gmail.com', NULL, 'applicant', 'active', '2025-05-07 05:36:28', '2025-05-07 05:36:44'),
  (28, 'CARATAO', '$2y$10$vMVPHmgH9rjZm2t/HmbggOmXUsJ9TBO3XsmW5mjIAP295M2Ha7rmi', '', '', 'charise012007@gmail.com', NULL, 'applicant', 'active', '2025-05-07 05:48:13', '2025-05-07 05:48:36'),
  (29, 'An.Catapang', '$2y$10$xQEj7./RtsUfYpWkHk8dL.HnLRe8DcOIRH8UwMyfDtdIqF9PtMPNK', '', '', 'catapang.an29@gmail.com', NULL, 'applicant', 'active', '2025-05-07 05:56:57', '2025-05-07 05:57:09'),
  (30, 'Alyanna T. Chavez', '$2y$10$ngFRM2Gl88BcRYBEBLRAlu0AeawHcsWsn2hYb/I4S1GKOjK1XH4BW', '', '', 'alyannachavez11@gmail.com', NULL, 'applicant', 'active', '2025-05-07 06:05:40', '2025-05-07 06:05:54'),
  (31, 'MARJHON', '$2y$10$v6kM3KoEq.F50aOcRTUp.uW/oNDwyZXPQSrr/bQlIlk5aD46v33TO', '', '', 'MARJHONCIRIACO66@GMAIL.COM', NULL, 'applicant', 'active', '2025-05-07 06:21:46', '2025-05-07 06:22:00'),
  (32, 'COSTOYKAIZA', '$2y$10$s23CsmKsr2uhWj2Rhe0Bp.qHPegUxdHz6/YNbrFXAoOr9EgWzvG5W', '', '', 'COSTOYKAIZA@GMAIL.COM', NULL, 'applicant', 'active', '2025-05-08 00:34:46', '2025-05-08 00:34:55'),
  (33, 'Lawrence Gabriel G. Cruz', '$2y$10$d2KnALM6fOhk/qShoFGwE.pDxAUZa8A4LjLtZeLLtQsf4dozcRu3K', '', '', 'lgcruz912@gmail.com', NULL, 'applicant', 'active', '2025-05-08 00:43:01', '2025-05-08 00:43:09'),
  (34, 'teddydelatorre', '$2y$10$oAVvkwWpECSBPAwk56n.nO0Rf2.KM8SgdgffX3a/9bs/GOxbzrdmW', '', '', 'teddydelatorre15@gmail.com', NULL, 'applicant', 'active', '2025-05-08 00:51:10', '2025-05-08 00:52:03'),
  (35, 'juswacones', '$2y$10$Xs5fu28faCsPN.aifuUC9OWyWHOL00rWwLpxpemQmcC8L/wnyXjKK', '', '', 'joshuaconsorte57@gmail.com', NULL, 'applicant', 'active', '2025-05-08 01:57:52', '2025-05-08 05:39:33'),
  (36, 'Sean Caine Demaisip', '$2y$10$SoI.0XALE9CkkaN/bc0RDeHvywa4jUABSKwX57nkTvWNNq5mgsW2W', '', '', 'seandemaisip@gmail.com', NULL, 'applicant', 'active', '2025-05-08 05:32:46', '2025-05-08 05:39:25'),
  (37, 'Rey Gabriel Dela Cruz', '$2y$10$tt/gZmCERAkxRKBErLDdA.4IP/Pd17PSUUeEiAB3Dcp4YKNJyO.aq', '', '', 'reygabrieldelacruz594@gmail.com', NULL, 'applicant', 'active', '2025-05-08 05:37:24', '2025-05-08 05:39:30'),
  (40, 'Gabriel Dela Cruz', '$2y$10$2WUzndshZ5xI/1aVB7OobOJFSgpwDjtH.Q/NXsPbO31V/FC7gxGze', '', '', '8027fudanshi@gmail.com', NULL, 'applicant', 'active', '2025-05-08 05:41:15', '2025-05-08 05:41:26'),
  (41, 'Gabriel V. Delera', '$2y$10$BWGaecnTaWESxEZcPF4PNONUK..rwwy6z/l5iwvv9/Hw8oOHhCLla', '', '', 'deleragabriel@gmail.com', NULL, 'applicant', 'active', '2025-05-08 05:51:17', '2025-05-08 05:51:35'),
  (42, 'ronmarizdelatorre', '$2y$10$al/I3YVC0gn67fHfOB0AQOgQMmiJZ4qZSlq8zVYQ3E/aiKyL9cdeW', '', '', 'ronmarizdelatorre@gmail.com', NULL, 'applicant', 'active', '2025-05-08 05:58:09', '2025-05-08 05:58:46'),
  (43, 'crisdeleon123', '$2y$10$2ZLWHmLqQnalLdM9C2NzzesrrKw1Ft16AN5RtIvNp9oQhFbiIZA4.', '', '', 'crisniccolodeleon5445@gmail.com', NULL, 'applicant', 'active', '2025-05-08 06:04:41', '2025-05-08 06:04:50'),
  (44, 'anthony07', '$2y$10$4zrTL7xpyNSU08V8dgFNkuZcr8roHLwH2FvoWUyoZp7OW25iuTp9G', '', '', 'anthonyartdemegillo@gmail.com', NULL, 'applicant', 'active', '2025-05-09 00:26:09', '2025-05-09 00:26:36'),
  (45, 'mattlaurentdiaz@gmail.com', '$2y$10$jQ4tr2L4GgvWMQ.clRIl6ugCfyuF38cAWPIPU4ho/0rxOmRyAtsr6', '', '', 'mattlaurentdiaz@gmail.com', NULL, 'applicant', 'active', '2025-05-09 00:34:23', '2025-05-09 00:34:36'),
  (46, 'JudeSteven', '$2y$10$NjThJe2.EZODN3zVfS1T2ueFgCPzBdwcyFRo0tDoZ0Kempv2Gf.Lu', '', '', 'judestevendoromal@gmail.com', NULL, 'applicant', 'active', '2025-05-09 00:41:16', '2025-05-09 00:41:33'),
  (47, 'fritzysabelle', '$2y$10$5pkKowyAd4lDd6pAaNcjf.xB/PsvFZOQ39t6XgwokgmXSVLZ6.3vi', '', '', 'fritzysabelle@gmail.com', NULL, 'applicant', 'active', '2025-05-09 00:49:20', '2025-05-09 00:49:34'),
  (48, 'jaylord dueñas', '$2y$10$oC0zTHSXtJtnafTpz2TGY.MtbDiluVD8RAMK4.Tmh1d5zTdwlOD/u', '', '', 'jaylord0609@gmail.com', NULL, 'applicant', 'active', '2025-05-09 01:02:51', '2025-05-09 01:03:15'),
  (49, 'ronnelesmillaren', '$2y$10$y4UrIVMPsCcl3XquzDcdcOknvEE06Y0sf6HtGT9HvIVaVs/8QNQZq', '', '', 'ronnelesmillaren123@gmail.com', NULL, 'applicant', 'active', '2025-05-09 05:13:16', '2025-05-09 05:30:53'),
  (50, 'erikaerillo', '$2y$10$B0Og/AtqYZjDA.pMBfzO2ebVvPhv3eDb4dgSN70B7kLw6NOyiIOQS', '', '', 'erikaerillo@gmail.com', NULL, 'applicant', 'active', '2025-05-09 05:20:30', '2025-05-09 05:30:47'),
  (51, 'Erich Grace', '$2y$10$GKST/vF6O7Rlx1QzKrZIk.nGOKyGQ6kWsHr.ENIGOGFRH6bMmzOAq', '', '', 'erichgracedupitas@gmail.com', NULL, 'applicant', 'active', '2025-05-09 05:34:07', '2025-05-13 00:46:56'),
  (52, 'Styrene', '$2y$10$xC6h2R3pbzP8WpUiPEpXFuqnr4YWWyohgGub8d8vZYMVmI3tghmTa', '', '', 'yreneesparcia@gmail.com', NULL, 'applicant', 'active', '2025-05-09 05:40:00', '2025-05-13 00:47:17'),
  (53, 'Joshua Espartero', '$2y$10$B1ltM6De25CKVQ9ZuQu8WOALMvcJxYk1KIRrQ7CwiAdSTvYdNitVS', '', '', 'joshuakendricespartero@gmail.com', NULL, 'applicant', 'active', '2025-05-09 05:47:03', '2025-05-13 00:47:13'),
  (54, 'Joules  Isaac D . Guntalidad', '$2y$10$d74meJxPXTVqZUl8zy/PquljfCW9NIYx7.8ePc3pwChqV9J0XKHmS', '', '', 'iscjoules@gmail.com', NULL, 'applicant', 'active', '2025-05-13 00:46:04', '2025-05-13 00:47:07'),
  (55, 'Irene Marie D. Hermosura', '$2y$10$ol51y8WOSmfhu6mzedKCBeHm.z/6ELAhoaQdGhuK89RSHbcR519fC', '', '', 'irenehermosura99@gmail.com', NULL, 'applicant', 'active', '2025-05-13 00:59:55', '2025-05-13 01:00:08'),
  (56, 'Ellaine Raquel Guzon', '$2y$10$i5Evo4X7zFQ4TDR8giA9XuQMYL/ewapCRYMPBiqL.vL09Ax..pjJS', '', '', 'ellaineraquelguzon@gmail.com', NULL, 'applicant', 'active', '2025-05-13 01:06:53', '2025-05-13 01:07:16'),
  (57, 'jessiejalandra', '$2y$10$6Y1TbgAeLn/BXIwOOHqjMu5uIbrBMFutbYxQdBX3sQwrkXe8i/zDe', '', '', 'jessiejalandra11@gmail.com', NULL, 'applicant', 'active', '2025-05-13 01:15:14', '2025-05-13 01:15:35'),
  (58, 'Nicole Jalique', '$2y$10$Nra/yuiThSWDp.DrldVplObA..bi64NaOUf3X0WcHO.1MbTgd6Xuy', '', '', 'jalique.nicole07@gmail.com', NULL, 'applicant', 'active', '2025-05-13 05:34:14', '2025-05-13 05:34:55'),
  (59, 'chenestjava', '$2y$10$L/v3aLEzEzypP9JxUPdnmezaahu7A5betW/LH6J2fZzk.mHxBtC9q', '', '', 'chenestjava@gmail.com', NULL, 'applicant', 'active', '2025-05-13 05:40:11', '2025-05-13 05:40:50'),
  (60, 'pnjimenez', '$2y$10$R7fXCYd9RO4SLC/25STL3eZm0CR7OIlCyA7ZTbWo2Cpqq8jV4XmpG', '', '', 'jimenezprincessnnn@gmail.com', NULL, 'applicant', 'active', '2025-05-13 05:48:03', '2025-05-13 05:48:29'),
  (61, 'johnchrisjimenez', '$2y$10$Owi3VZqpXIMzwn1ehl1Hiu5lVvRFkwQ1V7//fN6u3fZoQOJ0LK/cG', '', '', 'johnchrisjimenez@gmail.com', NULL, 'applicant', 'active', '2025-05-13 05:54:35', '2025-05-13 05:56:00'),
  (62, 'PRINCE LAMBO ON', '$2y$10$DsUUH1nf2rfjWhSAY2cXHeXPJvRV8mZ97XmIKTbw.S4hJr8AxvNny', '', '', 't7702865@gmail.com.com', NULL, 'applicant', 'active', '2025-05-14 00:45:51', '2025-05-14 00:46:03'),
  (63, 'Juntri', '$2y$10$yJz/UL20leyi0rCFPWtQJOEdK.tfWBnNFjMB74RngzQ97Xif15CFq', '', '', 'langreojuntri5@gmail.com', NULL, 'applicant', 'active', '2025-05-14 00:52:34', '2025-05-14 00:52:40'),
  (64, 'jhuliana mae', '$2y$10$CzthGPiOxcKjpbQJHtU19uw9C.hlh.HE7VBfNKt5qa3fVuklXwyRq', '', '', 'jhulianamaelirazan20@gmail.com', NULL, 'applicant', 'active', '2025-05-14 01:00:15', '2025-05-14 01:01:01'),
  (65, 'trisha.loberas', '$2y$10$VPoPoCBnMfmuTdSqopHdD.FvCMZZXQvxlwpiGOCoNhnId6OJtqO9a', '', '', 'trisha.loberas@gmail.com', NULL, 'applicant', 'active', '2025-05-14 05:35:56', '2025-05-14 05:36:26'),
  (66, 'Julianne May Lodovise', '$2y$10$aIf48hlZfOcI9k7Qk72Pzelxgd2YLvZhypCCKRzD94LYQ63DOzFGa', '', '', 'jmlodovise15@gmail.com', NULL, 'applicant', 'active', '2025-05-14 05:43:52', '2025-05-14 05:44:02'),
  (67, 'sheamabaquiao', '$2y$10$7tEBVc8BJc8pvhI6Qya7ZecGsMcsUTzANTVujGirGQZIYCjcJitYa', '', '', 'sheiyuhh3136@gmail.com', NULL, 'applicant', 'active', '2025-05-14 05:49:56', '2025-05-14 05:50:09'),
  (68, 'Angel P. Mag-usara', '$2y$10$f7McRn3X5gsd9ae.u4DSauSwnreQ3xf6GztxHJpZAX54wQTtpuIpu', '', '', 'angelmagusarsa1027@gmail.com', NULL, 'applicant', 'active', '2025-05-14 05:58:43', '2025-05-14 05:59:01'),
  (69, 'arlomagno', '$2y$10$5vaeqBQGuwQwK1gDN5i2telP6WRvzmT63BdM80KCQVsnXjIrJJsLS', '', '', '10102007aarlom@gmail.com', NULL, 'applicant', 'active', '2025-05-14 06:05:39', '2025-05-14 06:05:45'),
  (70, 'Cham Gazelle B.Mallo', '$2y$10$Tt/6nelID6FV/XQnBCICb.5FauPHoKSMeDEPuYviNfr9jMhIATiwC', '', '', 'chamelgalo@gmail.com', NULL, 'applicant', 'active', '2025-05-15 00:41:44', '2025-05-15 00:42:19'),
  (71, 'rekgab', '$2y$10$S9MRY1I8zoXfXQPdt9yTdOGe.GmlcN4e9/1zbqy3IsDbEhVHjxeM.', '', '', 'manualenriquegabrielj@gmail.com', NULL, 'applicant', 'active', '2025-05-15 01:06:19', '2025-05-15 01:06:53'),
  (72, 'Earl Floyd R. Mapisa', '$2y$10$NGTIgBysnLpJt.RMgJpwcOIJdjTgAXTvt2.BSOGjXJC4y9HaG6jmu', '', '', 'dungking67@gmail.com', NULL, 'applicant', 'active', '2025-05-15 01:13:40', '2025-05-15 01:47:09'),
  (73, 'Fermin', '$2y$10$dBvVpHx74BywrTiZ8xZoIu1BNnTBjXqi70t9kkJ9jpw9LKO4oq02C', '', '', 'marangafermin269@gmail.com', NULL, 'applicant', 'active', '2025-05-15 01:19:20', '2025-05-15 01:47:13'),
  (74, 'richardMula', '$2y$10$Or7w3ZBUptSkeUt1moDvaeIxRV.YhSdzhmBxExWtIN7OvN6P7MpAe', '', '', 'richardmartinitemula@gmail.com', NULL, 'applicant', 'active', '2025-05-15 05:37:28', '2025-05-15 05:38:41'),
  (75, 'kristian Paul M. Monton', '$2y$10$HgpFKRsQKMsppJIitkoxmeYTrlG36vsuOuD7nUM0cEk8SazWEwm5C', '', '', 'montonkristian@gmail.com', NULL, 'applicant', 'active', '2025-05-15 05:50:57', '2025-05-15 06:13:01'),
  (76, 'jr6664225', '$2y$10$jwoIt68hZXGAM//nt9OTLes7SF8e6HF50hxlCRBA3YMx0tn1eBbmW', '', '', '57mongcal@gmail.com', NULL, 'applicant', 'active', '2025-05-15 05:57:12', '2025-05-15 06:13:05'),
  (77, 'Mikan', '$2y$10$ULheMuDQZPHlJjVzfHTaauCd2PQV/zaWHSC.x0EAhqPh7Jx3L09s.', '', '', 'minada.mikan@gmail.com', NULL, 'applicant', 'active', '2025-05-15 06:04:03', '2025-05-15 06:12:56'),
  (78, 'Kent Yver', '$2y$10$89bNEjFFLQISn4.GE4YzheKSoIjRWL0z9kDOy.YiqIQxpGpJrOxHu', '', '', 'yvergamingyt@gmail.com', NULL, 'applicant', 'active', '2025-05-16 00:37:35', '2025-05-16 00:37:43'),
  (79, 'Marianne Nabalona', '$2y$10$rILZzExfrLBt8.3smazKauK4TFmyupxgIMl6E0hLFYLRjE3jiLYBG', '', '', 'marianneysabelle12@gmail.com', NULL, 'applicant', 'active', '2025-05-16 00:48:39', '2025-05-16 00:48:47'),
  (80, 'lancengitngit23', '$2y$10$4V/XqdMHGJvdBBpSgxhuH.uLgboL7M5abKcoW6zPT6CadClcNHpKm', '', '', 'lancengitngit@gmail.com', NULL, 'applicant', 'active', '2025-05-16 00:54:15', '2025-05-19 00:48:56'),
  (81, 'jayvee_pena', '$2y$10$BOiEdIB/LlKK1TIJDK7PJOFOml8t66vk7loRsAvb8RaXZeEJalg5.', '', '', 'jayveebpena@gmail.com', NULL, 'applicant', 'active', '2025-05-19 00:49:24', '2025-05-19 00:49:50'),
  (82, 'marie_rhyza', '$2y$10$AYC7JeC1Y/G8vTswaqqGwev1mNLfkNAuFVhNNOUoeC3ywc8Fbub6y', '', '', 'marierhyza@gmail.com', NULL, 'applicant', 'active', '2025-05-19 00:57:03', '2025-05-19 00:57:31'),
  (83, 'Jnicole', '$2y$10$Zjf47Y4jqIuzcHK7/FDPUu7eDSi7CD41Ww84F494friZK05IKZCjy', '', '', 'panchojustinnicole16@gmail.com', NULL, 'applicant', 'active', '2025-05-19 01:03:37', '2025-05-19 01:04:21'),
  (84, 'Dhan Azer Getida', '$2y$10$T.px6KW7sxhiCjIxBWfNf.ZhMV4D.IR1y2hh3LGDp05nOZ/xrgZXu', '', '', 'akensoyu@gmail.com', NULL, 'applicant', 'active', '2025-05-19 01:13:56', '2025-05-19 01:14:09'),
  (85, 'klauipenetrante', '$2y$10$.y/7o6L4GQ99Y2CzVGsebu63FE0ez59XcuvtBNMTdRaUxTrDpBikK', '', '', 'penetranteklaui@gmail.com', NULL, 'applicant', 'active', '2025-05-19 05:28:43', '2025-05-19 05:30:00'),
  (86, 'aerone_carl', '$2y$10$Io/5mBTnirU2pzuiZOUmsOAQsy.ZB85LzuKkp3RCxfH707xzjRUUi', '', '', 'aeronecarlpenol@gmail.com', NULL, 'applicant', 'active', '2025-05-19 05:35:55', '2025-05-19 05:45:56'),
  (87, 'Gael Perez', '$2y$10$rdn74Iw50crlf/a8s5Lvcuqqcg7PuG9Zv8PiHD6b7PReZV.EXx8xq', '', '', 'perezgael578@gmail.com', NULL, 'applicant', 'active', '2025-05-19 05:52:19', '2025-05-19 05:53:07'),
  (88, 'annaroa', '$2y$10$cjeE33SpXOImfgvVx537P.hs6GwwvNgQXdPPJmgVUAueWHrYgeOnu', '', '', 'annaroa4951@gmail.com', NULL, 'applicant', 'active', '2025-05-20 00:43:34', '2025-05-20 00:43:46'),
  (89, 'Jmrojo1213', '$2y$10$P67CrSXDOyhXCtS7VbUB0e9Fod.dG2lZxu45n99R6Qd6ydOPdX3by', '', '', 'mikhailsome13@gmail.com', NULL, 'applicant', 'active', '2025-05-20 00:53:58', '2025-05-20 00:54:07'),
  (90, 'rovicruales19', '$2y$10$oar5IRhd.hJQm2gOi0m05O6y59BMT34sPG1afV3Ah0UPs3oYsuUAe', '', '', 'rovicdoque19@gmail.com', NULL, 'applicant', 'active', '2025-05-20 01:05:39', '2025-05-20 01:05:52'),
  (91, 'sherahsalabania', '$2y$10$66OhTUcTTopneF19T5ngbefDGQjdQpkFRHuo.wiDyIzGHz.aFn/26', '', '', 'sherahsalabania@gmail.com', NULL, 'applicant', 'active', '2025-05-20 01:13:32', '2025-05-20 01:13:41'),
  (92, 'Joshcrivin', '$2y$10$sF.fVTA3KgYDDbzWsvctVOeeeta4Hr3uTMI/G2Af81HSYvtBcgEjq', '', '', 'jocour123@gmail.com', NULL, 'applicant', 'active', '2025-05-20 01:19:38', '2025-05-20 01:19:58'),
  (93, 'Jojie Glyn Salaver', '$2y$10$ZdoSbGZv5IUG.9sY2aJIHO39vcBrS4ghaO200bXyiWfe3GpZnWC2S', '', '', 'salaver.jojieglyn@gmail.com', NULL, 'applicant', 'active', '2025-05-20 05:35:27', '2025-05-20 05:35:39'),
  (94, 'John Michael T. Samar', '$2y$10$7mpwzCKOQT4WDo2qa1qLsuFliNrc2EXsvcWETZUEJZhZV4NyzpXMy', '', '', 'missing784@gmail.com', NULL, 'applicant', 'active', '2025-05-20 05:47:23', '2025-05-21 00:38:03'),
  (95, 'Marineth San Jose', '$2y$10$1V/QR6tbW3K4ooKPJQubXeZIS7y.h7Inl5U9P5JgoZjXlhac7zp9q', '', '', 'nethmndz31@gmail.com', NULL, 'applicant', 'active', '2025-05-20 05:56:07', '2025-05-21 00:38:10'),
  (96, 'sanorhaley@gmail.com', '$2y$10$A/ikrCgn.hhoxbAscRk.a.qpPB7LrKwkaagIyEPqZekFb/BM5UlGy', '', '', 'sanorhaley@gmail.com', NULL, 'applicant', 'active', '2025-05-20 06:04:09', '2025-05-21 00:37:49'),
  (97, 'savedorj1506', '$2y$10$GOta4Tx2veKa5OMkG83jDO3yJ40YuZaJI9A/kXNtLvRlcz8ek9LZe', '', '', 'savedorjenesis@gmail.com', NULL, 'applicant', 'active', '2025-05-20 06:12:23', '2025-05-21 00:37:59'),
  (98, 'Franfran536', '$2y$10$tpCid9n1XGqjmOvbeddX7.FZbLIga52o1QcmZaM/vM9HJHIgmor1O', '', '', 'franserdevilla3@gmail.com', NULL, 'applicant', 'active', '2025-05-21 00:37:43', '2025-05-21 00:37:55'),
  (99, 'SICATDANIELLA', '$2y$10$FcVPpysjwkneArMAhmD4juBKHAun6MW3D0rNiwgzlBWOtN4h5RZxq', '', '', 'daniellafsicat@gmail.com', NULL, 'applicant', 'active', '2025-05-21 00:47:19', '2025-05-21 00:47:36'),
  (100, 'Jose Gabriel P. Sucaldito', '$2y$10$QK.CwdP4TU.LqBi8InA2BO1xuyUNlmpJED1rUmQSp13iEXT7SylwK', '', '', 'josegabrielpsucaldito@gmail.com', NULL, 'applicant', 'active', '2025-05-21 05:41:22', '2025-05-21 05:41:44'),
  (101, 'franzdenielle1018', '$2y$10$zclXuyfMdDn9GAJJCTLfPu8WYHZowDP7maZv7y5wYxz/zaPRZfpMa', '', '', 'franzveraye0@gmail.com', NULL, 'applicant', 'active', '2025-05-21 05:47:15', '2025-05-21 05:47:23'),
  (102, 'Sabina Victoria', '$2y$10$EiHaW7h2.9DTAAh5rvUbYOBlLF4MKyD7Nc1sFHAH/SQ3J6/TCvIrq', '', '', 'svmtablazon@gmail.com', NULL, 'applicant', 'active', '2025-05-21 05:51:58', '2025-05-21 05:52:10'),
  (103, '2526A12927', '$2y$10$.WuBlXPtAeqJ6.IDGzr2V.IG627nKGT1t1QRDb/DSO5cYb/CXBe4i', '', '', 'jilliantauban3267@gmail.com', NULL, 'applicant', 'active', '2025-05-21 06:03:18', '2025-05-21 06:03:25'),
  (104, 'brenttingson25', '$2y$10$uaXNGoy.JQxDxvO9lJtJYOkZNbwZOTNeWgu22ZSiYq93AAppNSmDm', '', '', 'brenztingson@gmail.com', NULL, 'applicant', 'active', '2025-05-21 06:13:47', '2025-05-21 06:14:07'),
  (105, 'jillianvaldevia0703', '$2y$10$uSuWH9vOBGYW.x9PE/wg.upWJpmhDnPKlBiChaZaCr8szBFExkcwq', '', '', 'jillianvaldevia114@gmail.com', NULL, 'applicant', 'active', '2025-05-22 00:38:45', '2025-05-22 00:39:43'),
  (106, 'Jheniel Fernandez', '$2y$10$VdGhfoKIbeqGqagSC4ZOvuJ9Q0NSRqYno0AI3m/iG0S6OkZI45R46', '', '', 'jheniel.fernandez@outlook.com', NULL, 'applicant', 'active', '2025-05-22 00:46:30', '2025-05-22 00:46:56'),
  (107, 'ricapalmervelez@gmail.com', '$2y$10$uQR09ZP2M1755xvjCuxlLONzqLCGytMGI4.BarYiOhu9W11hW9PL6', '', '', 'ricapalmervelez@gmail.com', NULL, 'applicant', 'active', '2025-05-22 00:53:56', '2025-05-22 00:54:08'),
  (108, 'vergaracrystelgrace@gmail.com', '$2y$10$8Kt2.Fdstt7MalizmWQkruoqvd55hAolL1ttEgWemYuLHD4mURfiS', '', '', 'vergaracrystelgrace@gmail.com', NULL, 'applicant', 'active', '2025-05-22 01:01:22', '2025-05-22 01:01:37'),
  (109, 'GeraldVingco', '$2y$10$RUTEFxnDFDYZI.8nZQ88v.hzDDIpeojVO4o.ZPRNXSAdWqowqOm5.', '', '', 'geraldvingco123@gmail.com', NULL, 'applicant', 'active', '2025-05-22 01:07:23', '2025-05-22 01:07:37'),
  (110, 'TreyesRhean', '$2y$10$DPWwp4Gdw8V2YffTD6PgeOfcsV1UmBMY8j8q3/eh5mPvGYqbcQ1Bi', '', '', 'rheanlarenetreyes@gmail.com', NULL, 'applicant', 'active', '2025-05-22 01:17:23', '2025-05-22 01:17:31'),
  (111, 'Raziah Lee Torres', '$2y$10$QCJ8iw/s6C4BH436lvkP/.pypwxVVmJdqqUoFz8pbHmj9tC/BKdT2', '', '', 'raziahleetorres2@gmail.com', NULL, 'applicant', 'active', '2025-05-22 01:28:22', '2025-05-22 01:30:57');


  --
  -- Indexes for dumped tables
  --

  --
  -- Indexes for table `activity_log`
  --
  -- Fix activity_log table structure
  ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`); 

  --
  -- Indexes for table `applicants`
  --
  ALTER TABLE `applicants`
    ADD PRIMARY KEY (`applicant_id`),
    ADD KEY `user_id` (`user_id`),
    ADD KEY `primary_program_id` (`primary_program_id`),
    ADD KEY `secondary_program_id` (`secondary_program_id`);

  --
  -- Indexes for table `applications`
  --
  ALTER TABLE `applications`
    ADD PRIMARY KEY (`application_id`),
    ADD KEY `user_id` (`user_id`),
    ADD KEY `program_id` (`program_id`);

  --
  -- Indexes for table `colleges`
  --
  ALTER TABLE `colleges`
    ADD PRIMARY KEY (`college_id`),
    ADD UNIQUE KEY `college_code` (`college_code`);

  --
  -- Indexes for table `exam_registrations`
  --
  ALTER TABLE `exam_registrations`
    ADD PRIMARY KEY (`registration_id`),
    ADD UNIQUE KEY `unique_applicant_schedule` (`applicant_id`,`exam_schedule_id`),
    ADD KEY `exam_schedule_id` (`exam_schedule_id`);

  --
  -- Indexes for table `exam_results`
  --
  ALTER TABLE `exam_results`
    ADD PRIMARY KEY (`result_id`),
    ADD KEY `exam_id` (`exam_id`),
    ADD KEY `user_id` (`user_id`);

  --
  -- Indexes for table `exam_schedules`
  --
  ALTER TABLE `exam_schedules`
    ADD PRIMARY KEY (`exam_id`);

  --
  -- Indexes for table `exam_scores`
  --
  ALTER TABLE `exam_scores`
    ADD PRIMARY KEY (`score_id`),
    ADD UNIQUE KEY `registration_id` (`registration_id`),
    ADD KEY `assigned_program_id` (`assigned_program_id`),
    ADD KEY `interview_schedule_id` (`interview_schedule_id`);

  --
  -- Indexes for table `interviewers`
  --
  ALTER TABLE `interviewers`
    ADD PRIMARY KEY (`user_id`),
    ADD KEY `program_id` (`program_id`);

  --
  -- Indexes for table `interviews`
  --
  ALTER TABLE `interviews`
    ADD PRIMARY KEY (`interview_id`),
    ADD KEY `application_id` (`application_id`),
    ADD KEY `interviewer_id` (`interviewer_id`),
    ADD KEY `program_head_id` (`program_head_id`);

  --
  -- Indexes for table `interview_results`
  --
  ALTER TABLE `interview_results`
    ADD PRIMARY KEY (`result_id`),
    ADD KEY `applicant_id` (`applicant_id`),
    ADD KEY `interview_schedule_id` (`interview_schedule_id`),
    ADD KEY `interviewer_id` (`interviewer_id`);

  --
  -- Indexes for table `interview_schedules`
  --
  ALTER TABLE `interview_schedules`
    ADD PRIMARY KEY (`schedule_id`),
    ADD KEY `program_id` (`program_id`),
    ADD KEY `created_by` (`created_by`);

  --
  -- Indexes for table `notifications`
  --
  ALTER TABLE `notifications`
    ADD PRIMARY KEY (`notification_id`),
    ADD KEY `user_id` (`user_id`);

  --
  -- Indexes for table `programs`
  --
  ALTER TABLE `programs`
    ADD PRIMARY KEY (`program_id`),
    ADD KEY `college_id` (`college_id`),
    ADD KEY `programs_ibfk_2` (`program_head_id`);

  --
  -- Indexes for table `program_cutoffs`
  --
  ALTER TABLE `program_cutoffs`
    ADD PRIMARY KEY (`cutoff_id`),
    ADD KEY `program_id` (`program_id`);

  --
  -- Indexes for table `program_heads`
  --
  ALTER TABLE `program_heads`
    ADD PRIMARY KEY (`program_head_id`),
    ADD UNIQUE KEY `user_id` (`user_id`),
    ADD UNIQUE KEY `program_id` (`program_id`);

  --
  -- Indexes for table `program_rankings`
  --
  ALTER TABLE `program_rankings`
    ADD PRIMARY KEY (`ranking_id`),
    ADD KEY `applicant_id` (`applicant_id`),
    ADD KEY `program_id` (`program_id`),
    ADD KEY `assigned_program_id` (`assigned_program_id`);

  --
  -- Indexes for table `users`
  --
  ALTER TABLE `users`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `username` (`username`),
    ADD UNIQUE KEY `email` (`email`);

  --
  -- AUTO_INCREMENT for dumped tables
  --

  --
  -- AUTO_INCREMENT for table `activity_log`
  --
  ALTER TABLE `activity_log`
    MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

  --
  -- AUTO_INCREMENT for table `applicants`
  --
  ALTER TABLE `applicants`
    MODIFY `applicant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

  --
  -- AUTO_INCREMENT for table `applications`
  --
  ALTER TABLE `applications`
    MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

  --
  -- AUTO_INCREMENT for table `colleges`
  --
  ALTER TABLE `colleges`
    MODIFY `college_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

  --
  -- AUTO_INCREMENT for table `exam_registrations`
  --
  ALTER TABLE `exam_registrations`
    MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

  --
  -- AUTO_INCREMENT for table `exam_results`
  --
  ALTER TABLE `exam_results`
    MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

  --
  -- AUTO_INCREMENT for table `exam_schedules`
  --
  ALTER TABLE `exam_schedules`
    MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

  --
  -- AUTO_INCREMENT for table `exam_scores`
  --
  ALTER TABLE `exam_scores`
    MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT;

  --
  -- AUTO_INCREMENT for table `interviews`
  --
  ALTER TABLE `interviews`
    MODIFY `interview_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

  --
  -- AUTO_INCREMENT for table `interview_results`
  --
  ALTER TABLE `interview_results`
    MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT;

  --
  -- AUTO_INCREMENT for table `interview_schedules`
  --
  ALTER TABLE `interview_schedules`
    MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

  --
  -- AUTO_INCREMENT for table `notifications`
  --
  ALTER TABLE `notifications`
    MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

  --
  -- AUTO_INCREMENT for table `programs`
  --
  ALTER TABLE `programs`
    MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

  --
  -- AUTO_INCREMENT for table `program_cutoffs`
  --
  ALTER TABLE `program_cutoffs`
    MODIFY `cutoff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

  --
  -- AUTO_INCREMENT for table `program_heads`
  --
  ALTER TABLE `program_heads`
    MODIFY `program_head_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

  --
  -- AUTO_INCREMENT for table `program_rankings`
  --
  ALTER TABLE `program_rankings`
    MODIFY `ranking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

  --
  -- AUTO_INCREMENT for table `users`
  --
  ALTER TABLE `users`
    MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

  --
  -- Constraints for dumped tables
  --

  --
  -- Constraints for table `activity_log`
  --
  ALTER TABLE `activity_log`
    ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

  --
  -- Constraints for table `applicants`
  --
  ALTER TABLE `applicants`
    ADD CONSTRAINT `applicants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
    ADD CONSTRAINT `applicants_ibfk_2` FOREIGN KEY (`primary_program_id`) REFERENCES `programs` (`program_id`),
    ADD CONSTRAINT `applicants_ibfk_3` FOREIGN KEY (`secondary_program_id`) REFERENCES `programs` (`program_id`);

  --
  -- Constraints for table `applications`
  --
  ALTER TABLE `applications`
    ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;

  --
  -- Constraints for table `exam_registrations`
  --
  ALTER TABLE `exam_registrations`
    ADD CONSTRAINT `exam_registrations_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `exam_registrations_ibfk_2` FOREIGN KEY (`exam_schedule_id`) REFERENCES `exam_schedules` (`exam_id`) ON DELETE CASCADE;

  --
  -- Constraints for table `exam_results`
  --
  ALTER TABLE `exam_results`
    ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam_schedules` (`exam_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

  --
  -- Constraints for table `exam_scores`
  --
  ALTER TABLE `exam_scores`
    ADD CONSTRAINT `exam_scores_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `exam_registrations` (`registration_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `exam_scores_ibfk_2` FOREIGN KEY (`assigned_program_id`) REFERENCES `programs` (`program_id`) ON DELETE SET NULL,
    ADD CONSTRAINT `exam_scores_ibfk_3` FOREIGN KEY (`interview_schedule_id`) REFERENCES `interview_schedules` (`schedule_id`) ON DELETE SET NULL;

  --
  -- Constraints for table `interviewers`
  --
  ALTER TABLE `interviewers`
    ADD CONSTRAINT `interviewers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
    ADD CONSTRAINT `interviewers_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`);

  --
  -- Constraints for table `interviews`
  --
  ALTER TABLE `interviews`
    ADD CONSTRAINT `interviews_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`application_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `interviews_ibfk_2` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `interviews_ibfk_3` FOREIGN KEY (`program_head_id`) REFERENCES `program_heads` (`program_head_id`) ON DELETE CASCADE;

  --
  -- Constraints for table `interview_results`
  --
  ALTER TABLE `interview_results`
    ADD CONSTRAINT `interview_results_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`),
    ADD CONSTRAINT `interview_results_ibfk_2` FOREIGN KEY (`interview_schedule_id`) REFERENCES `interview_schedules` (`schedule_id`),
    ADD CONSTRAINT `interview_results_ibfk_3` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`user_id`);

  --
  -- Constraints for table `interview_schedules`
  --
  ALTER TABLE `interview_schedules`
    ADD CONSTRAINT `interview_schedules_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`),
    ADD CONSTRAINT `interview_schedules_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

  --
  -- Constraints for table `notifications`
  --
  ALTER TABLE `notifications`
    ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

  --
  -- Constraints for table `programs`
  --
  ALTER TABLE `programs`
    ADD CONSTRAINT `programs_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `programs_ibfk_2` FOREIGN KEY (`program_head_id`) REFERENCES `program_heads` (`program_head_id`) ON DELETE SET NULL;

  --
  -- Constraints for table `program_cutoffs`
  --
  ALTER TABLE `program_cutoffs`
    ADD CONSTRAINT `program_cutoffs_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;

  --
  -- Constraints for table `program_heads`
  --
  ALTER TABLE `program_heads`
    ADD CONSTRAINT `program_heads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `program_heads_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE;

  --
  -- Constraints for table `program_rankings`
  --
  ALTER TABLE `program_rankings`
    ADD CONSTRAINT `program_rankings_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`applicant_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `program_rankings_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `programs` (`program_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `program_rankings_ibfk_3` FOREIGN KEY (`assigned_program_id`) REFERENCES `programs` (`program_id`) ON DELETE SET NULL;
  COMMIT;

  /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
  /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
  /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
