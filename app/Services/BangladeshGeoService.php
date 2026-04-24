<?php

namespace App\Services;

class BangladeshGeoService
{
    public static function districts(): array
    {
        return [
            'Bagerhat', 'Bandarban', 'Barguna', 'Barishal', 'Bhola',
            'Bogura', 'Brahmanbaria', 'Chandpur', 'Chapainawabganj', 'Chuadanga',
            'Comilla', "Cox's Bazar", 'Dhaka', 'Dinajpur', 'Faridpur',
            'Feni', 'Gaibandha', 'Gazipur', 'Gopalganj', 'Habiganj',
            'Jamalpur', 'Jashore', 'Jhalokathi', 'Jhenaidah', 'Joypurhat',
            'Khagrachhari', 'Khulna', 'Kishoreganj', 'Kurigram', 'Kushtia',
            'Lakshmipur', 'Lalmonirhat', 'Madaripur', 'Magura', 'Manikganj',
            'Meherpur', 'Moulvibazar', 'Munshiganj', 'Mymensingh', 'Naogaon',
            'Narail', 'Narayanganj', 'Narsingdi', 'Natore', 'Netrokona',
            'Nilphamari', 'Noakhali', 'Pabna', 'Panchagarh', 'Patuakhali',
            'Pirojpur', 'Rajbari', 'Rajshahi', 'Rangamati', 'Rangpur',
            'Satkhira', 'Shariatpur', 'Sherpur', 'Sirajganj', 'Sunamganj',
            'Sylhet', 'Tangail', 'Thakurgaon',
        ];
    }

    public static function thanas(): array
    {
        return [
            'Dhaka'          => ['Adabor','Badda','Bangshal','Cantonment','Chawkbazar','Darus Salam','Demra','Dhanmondi','Dohar','Gulshan','Hazaribagh','Jatrabari','Kadamtali','Kafrul','Kalabagan','Kamrangirchar','Keraniganj','Khilgaon','Khilkhet','Kotwali','Lalbagh','Mirpur','Mohammadpur','Motijheel','Nawabganj','New Market','Pallabi','Paltan','Ramna','Sabujbagh','Shah Ali','Shahbagh','Shyampur','Sutrapur','Tejgaon','Turag','Uttara','Wari'],
            'Chittagong'     => ['Anwara','Banshkhali','Bayazid Bostami','Boalkhali','Chandgaon','Chandranaish','Chokoria','Double Mooring','Fatikchhari','Hathazari','Karnafully','Khulshi','Kotwali','Lohagara','Mirsharai','Pahartali','Panchlaish','Patiya','Rangunia','Raozan','Sandwip','Satkania','Sitakunda'],
            'Sylhet'         => ['Balaganj','Beanibazar','Bishwanath','Companiganj','Fenchuganj','Golapganj','Gowainghat','Jaintiapur','Kanaighat','Osmani Nagar','South Surma','Sylhet Sadar','Zakiganj'],
            'Rajshahi'       => ['Bagha','Bagmara','Boalia','Charghat','Durgapur','Godagari','Matihar','Mohanpur','Paba','Puthia','Rajpara','Shah Makhdum','Tanore'],
            'Khulna'         => ['Batiaghata','Dacope','Daulatpur','Dighalia','Dumuria','Khalishpur','Khan Jahan Ali','Khulna Sadar','Koyra','Paikgacha','Phultala','Rupsa','Sonadanga','Terokhada'],
            'Barishal'       => ['Agailjhara','Babuganj','Bakerganj','Banaripara','Gaurnadi','Hizla','Kotwali','Mehendiganj','Muladi','Wazirpur'],
            'Rangpur'        => ['Badarganj','Gangachara','Kaunia','Mithapukur','Pirgachha','Pirganj','Rangpur Sadar','Taraganj'],
            'Mymensingh'     => ['Bhaluka','Dhobaura','Fulbaria','Gaffargaon','Gauripur','Haluaghat','Ishwarganj','Muktagacha','Mymensingh Sadar','Nandail','Phulpur','Trishal'],
            'Gazipur'        => ['Gazipur Sadar','Kaliakair','Kaliganj','Kapasia','Sreepur','Tongi'],
            'Narayanganj'    => ['Araihazar','Bandar','Narayanganj Sadar','Rupganj','Sonargaon'],
            'Comilla'        => ['Barura','Brahmanpara','Burichang','Chandina','Chauddagram','Comilla Sadar','Daudkandi','Debidwar','Homna','Laksam','Meghna','Muradnagar','Nangalkot','Titas'],
            'Bogura'         => ['Adamdighi','Bogura Sadar','Dhunat','Dhupchanchia','Gabtali','Kahaloo','Nandigram','Sariakandi','Shajahanpur','Sherpur','Shibganj','Sonatola'],
            'Tangail'        => ['Basail','Bhuapur','Delduar','Dhanbari','Ghatail','Gopalpur','Kalihati','Madhupur','Mirzapur','Nagarpur','Sakhipur','Tangail Sadar'],
            'Dinajpur'       => ['Birampur','Birganj','Biral','Bochaganj','Chirirbandar','Dinajpur Sadar','Fulbari','Ghoraghat','Hakimpur','Kaharole','Khansama','Nawabganj','Parbatipur'],
            'Faridpur'       => ['Alfadanga','Bhanga','Boalmari','Charbhadrasan','Faridpur Sadar','Madhukhali','Nagarkanda','Sadarpur','Saltha'],
            'Pabna'          => ['Atgharia','Bera','Bhangura','Chatmohar','Faridpur','Ishwardi','Pabna Sadar','Santhia','Sujanagar'],
            "Cox's Bazar"    => ["Chakaria","Chokoria","Cox's Bazar Sadar","Kutubdia","Maheshkhali","Pekua","Ramu","Teknaf","Ukhia"],
            'Jashore'        => ['Abhaynagar','Bagherpara','Chaugachha','Jhikargachha','Keshabpur','Manirampur','Shahrasti','Jashore Sadar'],
            'Noakhali'       => ['Begumganj','Chatkhil','Companiganj','Hatiya','Kabirhat','Noakhali Sadar','Senbagh','Sonaimuri','Subarnachar'],
            'Brahmanbaria'   => ['Akhaura','Ashuganj','Banchharampur','Bijoynagar','Brahmanbaria Sadar','Kasba','Nabinagar','Nasirnagar','Sarail'],
            'Habiganj'       => ['Ajmiriganj','Bahubul','Bahubal','Baniachong','Chunarughat','Habiganj Sadar','Lakhai','Madhabpur','Nabiganj'],
            'Moulvibazar'    => ['Barlekha','Juri','Kamalganj','Kulaura','Moulvibazar Sadar','Rajnagar','Sreemangal'],
            'Sunamganj'      => ['Bishwamvarpur','Chhatak','Derai','Dharampasha','Dowarabazar','Jagannathpur','Jamalganj','Sulla','Sunamganj Sadar','Tahirpur'],
            'Kishoreganj'    => ['Austagram','Bajitpur','Bhairab','Hossainpur','Itna','Karimganj','Katiadi','Kishoreganj Sadar','Kuliarchar','Mithamain','Nikli','Pakundia','Tarail'],
            'Narsingdi'      => ['Belabo','Monohardi','Narsingdi Sadar','Palash','Raipura','Shibpur'],
            'Manikganj'      => ['Daulatpur','Ghior','Harirampur','Manikganj Sadar','Saturia','Shivalaya','Singair'],
            'Munshiganj'     => ['Gazaria','Louhajang','Munshiganj Sadar','Sirajdikhan','Sreenagar','Tongibari'],
            'Netrokona'      => ['Atpara','Barhatta','Durgapur','Kalmakanda','Kendua','Khaliajuri','Madan','Mohanganj','Netrokona Sadar','Purbadhala'],
            'Gopalganj'      => ['Gopalganj Sadar','Kashiani','Kotalipara','Muksudpur','Tungipara'],
            'Madaripur'      => ['Kalkini','Madaripur Sadar','Rajoir','Shibchar'],
            'Shariatpur'     => ['Bhedarganj','Damudya','Gosairhat','Naria','Shariatpur Sadar','Zajira'],
            'Rajbari'        => ['Baliakandi','Goalanda','Kalukhali','Pangsha','Rajbari Sadar'],
            'Natore'         => ['Bagatipara','Baraigram','Gurudaspur','Lalpur','Natore Sadar','Singra'],
            'Sirajganj'      => ['Belkuchi','Chauhali','Enayetpur','Kamarkhand','Kazipur','Raiganj','Shahjadpur','Sirajganj Sadar','Tarash','Ullapara'],
            'Naogaon'        => ['Atrai','Badalgachhi','Dhamoirhat','Mahadebpur','Manda','Mohadevpur','Naogaon Sadar','Niamatpur','Patnitala','Porsha','Raninagar','Sapahar'],
            'Chapainawabganj'=> ['Bholahat','Chapainawabganj Sadar','Gomastapur','Nachole','Shibganj'],
            'Joypurhat'      => ['Akkelpur','Joypurhat Sadar','Kalai','Khetlal','Panchbibi'],
            'Nilphamari'     => ['Dimla','Domar','Jaldhaka','Kishoreganj','Nilphamari Sadar','Saidpur'],
            'Gaibandha'      => ['Fulchhari','Gaibandha Sadar','Gobindaganj','Palashbari','Sadullapur','Saghata','Sundarganj'],
            'Kurigram'       => ['Bhurungamari','Chilmari','Kurigram Sadar','Nageshwari','Phulbari','Rajibpur','Rajarhat','Raumari','Ulipur'],
            'Lalmonirhat'    => ['Aditmari','Hatibandha','Kaliganj','Lalmonirhat Sadar','Patgram'],
            'Panchagarh'     => ['Atwari','Boda','Debiganj','Panchagarh Sadar','Tetulia'],
            'Thakurgaon'     => ['Baliadangi','Haripur','Pirganj','Ranisankail','Thakurgaon Sadar'],
            'Kushtia'        => ['Bheramara','Daulatpur','Khoksa','Kumarkhali','Kushtia Sadar','Mirpur'],
            'Meherpur'       => ['Gangni','Meherpur Sadar','Mujibnagar'],
            'Jhenaidah'      => ['Harinakunda','Jhenaidah Sadar','Kaliganj','Kotchandpur','Maheshpur','Shailkupa'],
            'Magura'         => ['Magura Sadar','Mohammadpur','Shalikha','Sreepur'],
            'Narail'         => ['Kalia','Lohagara','Narail Sadar'],
            'Satkhira'       => ['Assasuni','Debhata','Kalaroa','Kaliganj','Satkhira Sadar','Shyamnagar','Tala'],
            'Bagerhat'       => ['Bagerhat Sadar','Chitalmari','Fakirhat','Kachua','Mollahat','Mongla','Morrelganj','Rampal','Sarankhola'],
            'Khagrachhari'   => ['Dighinala','Guimara','Khagrachhari Sadar','Lakshmichhari','Mahalchhari','Manikchhari','Matiranga','Panchhari','Ramgarh'],
            'Rangamati'      => ['Bagaichhari','Barkal','Belaichhari','Juraichhari','Kaptai','Kawkhali','Langadu','Naniarchar','Rajasthali','Rangamati Sadar'],
            'Bandarban'      => ['Alikadam','Bandarban Sadar','Lama','Naikhongchhari','Rowangchhari','Ruma','Thanchi'],
            'Lakshmipur'     => ['Comillaour','Kamalnagar','Lakshmipur Sadar','Ramganj','Ramgati','Roypur'],
            'Feni'           => ['Daganbhuiyan','Feni Sadar','Fulgazi','Parshuram','Sonagazi'],
            'Chandpur'       => ['Chandpur Sadar','Faridganj','Haim Char','Hajiganj','Kachua','Matlab Dakshin','Matlab Uttar','Shahrasti'],
            'Jhalokathi'     => ['Jhalokathi Sadar','Kathalia','Nalchity','Rajapur'],
            'Patuakhali'     => ['Bauphal','Dashmina','Dumki','Galachipa','Kalapara','Mirzaganj','Patuakhali Sadar','Rangabali'],
            'Barguna'        => ['Amtali','Bamna','Barguna Sadar','Betagi','Patharghata','Taltali'],
            'Pirojpur'       => ['Bhandaria','Indurkani','Kawkhali','Mathbaria','Nazirpur','Pirojpur Sadar','Zianagar'],
            'Bhola'          => ['Bhola Sadar','Borhanuddin','Char Fasson','Daulatkhan','Lalmohan','Manpura','Tazumuddin'],
        ];
    }

    public static function thanasForDistrict(string $district): array
    {
        $all = self::thanas();
        return $all[$district] ?? [];
    }
}
