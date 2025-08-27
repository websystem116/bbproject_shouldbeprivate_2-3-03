import os
import cv2

from matplotlib import pyplot as plt
import shutil

from decimal import Decimal
import math

def decimal_normalize(f):
    text = str(f)
    while True:
        if ("." in text and text[-1] == "0") or (text[-1] == "."):
            text = text[:-1]
            continue
        break
    return text

filename = '/var/www/html/shinzemi/storage/app/upFiles/ancake-1.jpg'
# filename = '/var/www/html/shinzemi/storage/app/upFiles/an1.jpg'
# filename = '/var/www/html/shinzemi/storage/app/upFiles/20220523173424390_page-0001.jpg'

# shutil.rmtree('./ancake/images/')
# os.mkdir('./ancake/images/',777)

# files = glob.glob('/var/www/html/shinzemi/storage/app/upFiles/*.jpg')

img = cv2.imread(filename)

# imgの高さと幅を取得
img_height, img_width,channel = img.shape[:3]
# 係数計算
num = img_width / 2483
# 切り上げ
width = f'{num:.1f}'
# 係数計算
num2 = img_height / 3506
height = f'{num2:.1f}'

########################################
# アンケート番号
########################################
id_width_first = Decimal(width) * 200
id_width_first =int(math.floor(float(id_width_first)))

id_width_end = Decimal(width) * 400
id_width_end =int(math.floor(float(id_width_end)))

id_height_first = Decimal(height) * 1800
id_height_first =int(math.floor(float(id_height_first)))

id_height_end = Decimal(height) * 2350
id_height_end =int(math.floor(float(id_height_end)))

id = img[id_width_first:id_width_end,id_height_first:id_height_end] 

#####################################
# 学年 
#######################################
school_year_width_first = Decimal(width) * 350
school_year_width_first = int(math.floor(float(school_year_width_first)))

school_year_width_end = Decimal(width) * 1500
school_year_width_end = int(math.floor(float(school_year_width_end)))

school_year_height_first = Decimal(height) * 500
school_year_height_first =int(math.floor(float(school_year_height_first)))

school_year_height_end = Decimal(height) * 660
school_year_height_end = int(math.floor(float(school_year_height_end)))

school_year = img[school_year_height_first:school_year_height_end,school_year_width_first:school_year_width_end]

##############################################
# 英語クラス
###############################################
e_class_width_first = Decimal(width) * 730
e_class_width_first =int(math.floor(float(e_class_width_first)))

e_class_width_end = Decimal(width) * 900
e_class_width_end =int(math.floor(float(e_class_width_end)))

e_class_height_first = Decimal(height) * 360
e_class_height_first =int(math.floor(float(e_class_height_first)))

e_class_height_end = Decimal(height) * 860
e_class_height_end =int(math.floor(float(e_class_height_end)))

e_class = img[e_class_width_first:e_class_width_end,e_class_height_first:e_class_height_end] #英語クラス

############################################
# 英語　質問１ heightがずれるから＋１０
############################################
e_1_height_first = Decimal(height) * 910
e_1_height_first =int(math.floor(float(e_1_height_first)))

e_1_height_end = Decimal(height) * 1060
e_1_height_end =int(math.floor(float(e_1_height_end)))

e_1_width_first = Decimal(width) * 350
e_1_width_first =int(math.floor(float(e_1_width_first)))

e_1_width_end = Decimal(width) * 780
e_1_width_end =int(math.floor(float(e_1_width_end)))

e_1 = img[e_1_height_first:e_1_height_end,e_1_width_first:e_1_width_end] #英語クラス


############################################
# 英語　質問2 heightがずれるから＋１０
############################################
e_2_width_first = Decimal(width) * 350
e_2_width_first =int(math.floor(float(e_2_width_first)))

e_2_width_end = Decimal(width) * 780
e_2_width_end =int(math.floor(float(e_2_width_end)))

e_2_height_first = Decimal(height) * 1040
e_2_height_first =int(math.floor(float(e_2_height_first)))

e_2_height_end = Decimal(height) * 1190
e_2_height_end =int(math.floor(float(e_2_height_end)))

e_2 = img[e_2_height_first:e_2_height_end,e_2_width_first:e_2_width_end] #英語クラス

############################################
# 英語　質問3 heightがずれるから＋１０
############################################
e_3_width_first = Decimal(width) * 350
e_3_width_first =int(math.floor(float(e_3_width_first)))

e_3_width_end = Decimal(width) * 780
e_3_width_end =int(math.floor(float(e_3_width_end)))

e_3_height_first = Decimal(height) * 1190
e_3_height_first =int(math.floor(float(e_3_height_first)))

e_3_height_end = Decimal(height) * 1340
e_3_height_end =int(math.floor(float(e_3_height_end)))

e_3 = img[e_3_height_first:e_3_height_end,e_3_width_first:e_3_width_end]

############################################
# 英語　質問4 heightがずれるから＋１０
############################################
e_4_width_first = Decimal(width) * 350
e_4_width_first =int(math.floor(float(e_4_width_first)))

e_4_width_end = Decimal(width) * 780
e_4_width_end =int(math.floor(float(e_4_width_end)))

e_4_height_first = Decimal(height) * 1340
e_4_height_first =int(math.floor(float(e_4_height_first)))

e_4_height_end = Decimal(height) * 1490
e_4_height_end =int(math.floor(float(e_4_height_end)))

e_4 = img[e_4_height_first:e_4_height_end,e_4_width_first:e_4_width_end]

############################################
# 英語　質問5 heightがずれるから＋１０
############################################
e_5_width_first = Decimal(width) * 350
e_5_width_first =int(math.floor(float(e_5_width_first)))

e_5_width_end = Decimal(width) * 780
e_5_width_end =int(math.floor(float(e_5_width_end)))

e_5_height_first = Decimal(height) * 1470
e_5_height_first =int(math.floor(float(e_5_height_first)))

e_5_height_end = Decimal(height) * 1620
e_5_height_end =int(math.floor(float(e_5_height_end)))

e_5 = img[e_5_height_first:e_5_height_end,e_5_width_first:e_5_width_end]

############################################
# 英語　質問6 heightがずれるから＋１０
############################################
e_6_width_first = Decimal(width) * 350
e_6_width_first =int(math.floor(float(e_6_width_first)))

e_6_width_end = Decimal(width) * 780
e_6_width_end =int(math.floor(float(e_6_width_end)))

e_6_height_first = Decimal(height) * 1600
e_6_height_first =int(math.floor(float(e_6_height_first)))

e_6_height_end = Decimal(height) * 1750
e_6_height_end =int(math.floor(float(e_6_height_end)))

e_6 = img[e_6_height_first:e_6_height_end,e_6_width_first:e_6_width_end]

############################################
# 英語　質問7 heightがずれるから＋１０
############################################
e_7_width_first = Decimal(width) * 350
e_7_width_first =int(math.floor(float(e_7_width_first)))

e_7_width_end = Decimal(width) * 780
e_7_width_end =int(math.floor(float(e_7_width_end)))

e_7_height_first = Decimal(height) * 1750
e_7_height_first =int(math.floor(float(e_7_height_first)))

e_7_height_end = Decimal(height) * 1930
e_7_height_end =int(math.floor(float(e_7_height_end)))

e_7 = img[e_7_height_first:e_7_height_end,e_7_width_first:e_7_width_end]

############################################
# 理科クラス　
############################################
s_class_width_first = Decimal(width) * 1110
s_class_width_first =int(math.floor(float(s_class_width_first)))

s_class_width_end = Decimal(width) * 1650
s_class_width_end =int(math.floor(float(s_class_width_end)))

s_class_height_first = Decimal(height) * 730
s_class_height_first =int(math.floor(float(s_class_height_first)))

s_class_height_end = Decimal(height) * 900
s_class_height_end =int(math.floor(float(s_class_height_end)))

s_class = img[s_class_height_first:s_class_height_end,s_class_width_first:s_class_width_end]

############################################
# 理科質問1
############################################
s_1_width_first = Decimal(width) * 1110
s_1_width_first =int(math.floor(float(s_1_width_first)))

s_1_width_end = Decimal(width) * 1580
s_1_width_end =int(math.floor(float(s_1_width_end)))

s_1_height_first = Decimal(height) * 900
s_1_height_first =int(math.floor(float(s_1_height_first)))

s_1_height_end = Decimal(height) * 1050
s_1_height_end =int(math.floor(float(s_1_height_end)))

s_1 = img[s_1_height_first:s_1_height_end,s_1_width_first:s_1_width_end]

############################################
# 理科質問2
############################################
s_2_width_first = Decimal(width) * 1110
s_2_width_first =int(math.floor(float(s_2_width_first)))

s_2_width_end = Decimal(width) * 1580
s_2_width_end =int(math.floor(float(s_2_width_end)))

s_2_height_first = Decimal(height) * 900
s_2_height_first =int(math.floor(float(s_2_height_first)))

s_2_height_end = Decimal(height) * 1050
s_2_height_end =int(math.floor(float(s_2_height_end)))

s_2 = img[s_2_height_first:s_2_height_end,s_2_width_first:s_2_width_end]

############################################
# 理科質問3
############################################
s_3_width_first = Decimal(width) * 1110
s_3_width_first =int(math.floor(float(s_3_width_first)))

s_3_width_end = Decimal(width) * 1580
s_3_width_end =int(math.floor(float(s_3_width_end)))

s_3_height_first = Decimal(height) * 1180
s_3_height_first =int(math.floor(float(s_3_height_first)))

s_3_height_end = Decimal(height) * 1330
s_3_height_end =int(math.floor(float(s_3_height_end)))

s_3 = img[s_3_height_first:s_3_height_end,s_3_width_first:s_3_width_end]

# s_3 = img[1180:1330,1110:1580] #質問3

############################################
# 理科質問4
############################################
s_4_width_first = Decimal(width) * 1110
s_4_width_first =int(math.floor(float(s_4_width_first)))

s_4_width_end = Decimal(width) * 1580
s_4_width_end =int(math.floor(float(s_4_width_end)))

s_4_height_first = Decimal(height) * 1330
s_4_height_first =int(math.floor(float(s_4_height_first)))

s_4_height_end = Decimal(height) * 1480
s_4_height_end =int(math.floor(float(s_4_height_end)))

s_4 = img[s_4_height_first:s_4_height_end,s_4_width_first:s_4_width_end]


# s_4 = img[1330:1480,1110:1580] #質問4

############################################
# 理科質問5
############################################
s_5_width_first = Decimal(width) * 1110
s_5_width_first =int(math.floor(float(s_5_width_first)))

s_5_width_end = Decimal(width) * 1580
s_5_width_end =int(math.floor(float(s_5_width_end)))

s_5_height_first = Decimal(height) * 1460
s_5_height_first =int(math.floor(float(s_5_height_first)))

s_5_height_end = Decimal(height) * 1610
s_5_height_end =int(math.floor(float(s_5_height_end)))

s_5 = img[s_5_height_first:s_5_height_end,s_5_width_first:s_5_width_end]

# s_5 = img[1460:1610,1110:1580] #質問5

############################################
# 理科質問6
############################################
s_6_width_first = Decimal(width) * 1110
s_6_width_first =int(math.floor(float(s_6_width_first)))

s_6_width_end = Decimal(width) * 1580
s_6_width_end =int(math.floor(float(s_6_width_end)))

s_6_height_first = Decimal(height) * 1590
s_6_height_first =int(math.floor(float(s_6_height_first)))

s_6_height_end = Decimal(height) * 1740
s_6_height_end =int(math.floor(float(s_6_height_end)))

s_6 = img[s_6_height_first:s_6_height_end,s_6_width_first:s_6_width_end]

# s_6 = img[1590:1740,1110:1580] #質問6


############################################
# 理科質問7
############################################
s_7_width_first = Decimal(width) * 1110
s_7_width_first =int(math.floor(float(s_7_width_first)))

s_7_width_end = Decimal(width) * 1580
s_7_width_end =int(math.floor(float(s_7_width_end)))

s_7_height_first = Decimal(height) * 1740
s_7_height_first =int(math.floor(float(s_7_height_first)))

s_7_height_end = Decimal(height) * 1920
s_7_height_end =int(math.floor(float(s_7_height_end)))

s_7 = img[s_7_height_first:s_7_height_end,s_7_width_first:s_7_width_end]

# s_7 = img[1740:1920,1110:1580] #質問7

############################################
# 数学クラス　
############################################
m_class_width_first = Decimal(width) * 1900
m_class_width_first =int(math.floor(float(m_class_width_first)))

m_class_width_end = Decimal(width) * 2450
m_class_width_end =int(math.floor(float(m_class_width_end)))

m_class_height_first = Decimal(height) * 730
m_class_height_first =int(math.floor(float(m_class_height_first)))

m_class_height_end = Decimal(height) * 900
m_class_height_end =int(math.floor(float(m_class_height_end)))

m_class = img[m_class_height_first:m_class_height_end,m_class_width_first:m_class_width_end]

# m_class = img[730:900,1900:2450] #数学クラス

############################################
# 数学質問1
############################################
m_1_width_first = Decimal(width) * 1900
m_1_width_first =int(math.floor(float(m_1_width_first)))

m_1_width_end = Decimal(width) * 2350
m_1_width_end =int(math.floor(float(m_1_width_end)))

m_1_height_first = Decimal(height) * 900
m_1_height_first =int(math.floor(float(m_1_height_first)))

m_1_height_end = Decimal(height) * 1050
m_1_height_end =int(math.floor(float(m_1_height_end)))

m_1 = img[m_1_height_first:m_1_height_end,m_1_width_first:m_1_width_end]

# m_1 = img[900:1050,1900:2350] #質問1

############################################
# 数学質問2
############################################
m_2_width_first = Decimal(width) * 1900
m_2_width_first =int(math.floor(float(m_2_width_first)))

m_2_width_end = Decimal(width) * 2350
m_2_width_end =int(math.floor(float(m_2_width_end)))

m_2_height_first = Decimal(height) * 1030
m_2_height_first =int(math.floor(float(m_2_height_first)))

m_2_height_end = Decimal(height) * 1180
m_2_height_end =int(math.floor(float(m_2_height_end)))

m_2 = img[m_2_height_first:m_2_height_end,m_2_width_first:m_2_width_end]

# m_2 = img[1030:1180,1900:2350] #質問2

############################################
# 数学質問3
############################################
m_3_width_first = Decimal(width) * 1900
m_3_width_first =int(math.floor(float(m_3_width_first)))

m_3_width_end = Decimal(width) * 2350
m_3_width_end =int(math.floor(float(m_3_width_end)))

m_3_height_first = Decimal(height) * 1180
m_3_height_first =int(math.floor(float(m_3_height_first)))

m_3_height_end = Decimal(height) * 1330
m_3_height_end =int(math.floor(float(m_3_height_end)))

m_3 = img[m_3_height_first:m_3_height_end,m_3_width_first:m_3_width_end]

# m_3 = img[1180:1330,1900:2350] #質問3

############################################
# 数学質問4
############################################
m_4_width_first = Decimal(width) * 1900
m_4_width_first =int(math.floor(float(m_4_width_first)))

m_4_width_end = Decimal(width) * 2350
m_4_width_end =int(math.floor(float(m_4_width_end)))

m_4_height_first = Decimal(height) * 1330
m_4_height_first =int(math.floor(float(m_4_height_first)))

m_4_height_end = Decimal(height) * 1480
m_4_height_end =int(math.floor(float(m_4_height_end)))

m_4 = img[m_4_height_first:m_4_height_end,m_4_width_first:m_4_width_end]

# m_4 = img[1330:1480,1900:2350] #質問4

############################################
# 数学質問5
############################################
m_5_width_first = Decimal(width) * 1900
m_5_width_first =int(math.floor(float(m_5_width_first)))

m_5_width_end = Decimal(width) * 2350
m_5_width_end =int(math.floor(float(m_5_width_end)))

m_5_height_first = Decimal(height) * 1460
m_5_height_first =int(math.floor(float(m_5_height_first)))

m_5_height_end = Decimal(height) * 1610
m_5_height_end =int(math.floor(float(m_5_height_end)))

m_5 = img[m_5_height_first:m_5_height_end,m_5_width_first:m_5_width_end]
# m_5 = img[1460:1610,1900:2350] #質問5

############################################
# 数学質問6
############################################
m_6_width_first = Decimal(width) * 1900
m_6_width_first =int(math.floor(float(m_6_width_first)))

m_6_width_end = Decimal(width) * 2350
m_6_width_end =int(math.floor(float(m_6_width_end)))

m_6_height_first = Decimal(height) * 1590
m_6_height_first =int(math.floor(float(m_6_height_first)))

m_6_height_end = Decimal(height) * 1740
m_6_height_end =int(math.floor(float(m_6_height_end)))

m_6 = img[m_6_height_first:m_6_height_end,m_6_width_first:m_6_width_end]

# m_6 = img[1590:1740,1900:2350] #質問6

############################################
# 数学質問7
############################################
m_7_width_first = Decimal(width) * 1900
m_7_width_first =int(math.floor(float(m_7_width_first)))

m_7_width_end = Decimal(width) * 2350
m_7_width_end =int(math.floor(float(m_7_width_end)))

m_7_height_first = Decimal(height) * 1740
m_7_height_first =int(math.floor(float(m_7_height_first)))

m_7_height_end = Decimal(height) * 1920
m_7_height_end =int(math.floor(float(m_7_height_end)))

m_7 = img[m_7_height_first:m_7_height_end,m_7_width_first:m_7_width_end]

# m_7 = img[1740:1920,1900:2350] #質問7


############################################
# 国語クラス　
############################################
j_class_width_first = Decimal(width) * 350
j_class_width_first =int(math.floor(float(j_class_width_first)))

j_class_width_end = Decimal(width) * 850
j_class_width_end =int(math.floor(float(j_class_width_end)))

j_class_height_first = Decimal(height) * 1950
j_class_height_first =int(math.floor(float(j_class_height_first)))

j_class_height_end = Decimal(height) * 2130
j_class_height_end =int(math.floor(float(j_class_height_end)))

j_class = img[j_class_height_first:j_class_height_end,j_class_width_first:j_class_width_end]

# j_class = img[1950:2130,350:850] #国語クラス

############################################
# 国語質問1 +10
############################################
j_1_width_first = Decimal(width) * 350
j_1_width_first =int(math.floor(float(j_1_width_first)))

j_1_width_end = Decimal(width) * 780
j_1_width_end =int(math.floor(float(j_1_width_end)))

j_1_height_first = Decimal(height) * 2110
j_1_height_first =int(math.floor(float(j_1_height_first)))

j_1_height_end = Decimal(height) * 2260
j_1_height_end =int(math.floor(float(j_1_height_end)))

j_1 = img[j_1_height_first:j_1_height_end,j_1_width_first:j_1_width_end]


# j_1 = img[2100:2250,350:780] #国語質問1


############################################
# 国語質問2 +10
############################################
j_2_width_first = Decimal(width) * 350
j_2_width_first =int(math.floor(float(j_2_width_first)))

j_2_width_end = Decimal(width) * 780
j_2_width_end =int(math.floor(float(j_2_width_end)))

j_2_height_first = Decimal(height) * 2260
j_2_height_first =int(math.floor(float(j_2_height_first)))

j_2_height_end = Decimal(height) * 2410
j_2_height_end =int(math.floor(float(j_2_height_end)))

j_2 = img[j_2_height_first:j_2_height_end,j_2_width_first:j_2_width_end]


# j_2 = img[2250:2400,350:780] #国語質問2

############################################
# 国語質問3 +10
############################################
j_3_width_first = Decimal(width) * 350
j_3_width_first =int(math.floor(float(j_3_width_first)))

j_3_width_end = Decimal(width) * 780
j_3_width_end =int(math.floor(float(j_3_width_end)))

j_3_height_first = Decimal(height) * 2410
j_3_height_first =int(math.floor(float(j_3_height_first)))

j_3_height_end = Decimal(height) * 2560
j_3_height_end =int(math.floor(float(j_3_height_end)))

j_3 = img[j_3_height_first:j_3_height_end,j_3_width_first:j_3_width_end]

# j_3 = img[2400:2550,350:780] #国語質問3

############################################
# 国語質問4 +10
############################################
j_4_width_first = Decimal(width) * 350
j_4_width_first =int(math.floor(float(j_4_width_first)))

j_4_width_end = Decimal(width) * 780
j_4_width_end =int(math.floor(float(j_4_width_end)))

j_4_height_first = Decimal(height) * 2540
j_4_height_first =int(math.floor(float(j_4_height_first)))

j_4_height_end = Decimal(height) * 2690
j_4_height_end =int(math.floor(float(j_4_height_end)))

j_4 = img[j_4_height_first:j_4_height_end,j_4_width_first:j_4_width_end]

# j_4 = img[2530:2680,350:780] #国語質問4

############################################
# 国語質問5 +10
############################################
j_5_width_first = Decimal(width) * 350
j_5_width_first =int(math.floor(float(j_5_width_first)))

j_5_width_end = Decimal(width) * 780
j_5_width_end =int(math.floor(float(j_5_width_end)))

j_5_height_first = Decimal(height) * 2680
j_5_height_first =int(math.floor(float(j_5_height_first)))

j_5_height_end = Decimal(height) * 2840
j_5_height_end =int(math.floor(float(j_5_height_end)))

j_5 = img[j_5_height_first:j_5_height_end,j_5_width_first:j_5_width_end]


# j_5 = img[2670:2830,350:780] #国語質問5


############################################
# 国語質問6 +10
############################################
j_6_width_first = Decimal(width) * 350
j_6_width_first =int(math.floor(float(j_6_width_first)))

j_6_width_end = Decimal(width) * 780
j_6_width_end =int(math.floor(float(j_6_width_end)))

j_6_height_first = Decimal(height) * 2820
j_6_height_first =int(math.floor(float(j_6_height_first)))

j_6_height_end = Decimal(height) * 2980
j_6_height_end =int(math.floor(float(j_6_height_end)))

j_6 = img[j_6_height_first:j_6_height_end,j_6_width_first:j_6_width_end]


# j_6 = img[2810:2970,350:780] #国語質問6


############################################
# 国語質問7 +10
############################################
j_7_width_first = Decimal(width) * 350
j_7_width_first =int(math.floor(float(j_7_width_first)))

j_7_width_end = Decimal(width) * 780
j_7_width_end =int(math.floor(float(j_7_width_end)))

j_7_height_first = Decimal(height) * 2970
j_7_height_first =int(math.floor(float(j_7_height_first)))

j_7_height_end = Decimal(height) * 3130
j_7_height_end =int(math.floor(float(j_7_height_end)))

j_7 = img[j_7_height_first:j_7_height_end,j_7_width_first:j_7_width_end]

# j_7 = img[2960:3120,350:780] #国語質問7


############################################
# 社会クラス　
############################################
so_class_width_first = Decimal(width) * 1110
so_class_width_first =int(math.floor(float(so_class_width_first)))

so_class_width_end = Decimal(width) * 1650
so_class_width_end =int(math.floor(float(so_class_width_end)))

so_class_height_first = Decimal(height) * 1950
so_class_height_first =int(math.floor(float(so_class_height_first)))

so_class_height_end = Decimal(height) * 2130
so_class_height_end =int(math.floor(float(so_class_height_end)))

so_class = img[so_class_height_first:so_class_height_end,so_class_width_first:so_class_width_end]


# so_class = img[1950:2130,1110:1650] #社会クラス


############################################
# 社会質問1 +10
############################################
so_1_width_first = Decimal(width) * 1110
so_1_width_first =int(math.floor(float(so_1_width_first)))

so_1_width_end = Decimal(width) * 1580
so_1_width_end =int(math.floor(float(so_1_width_end)))

so_1_height_first = Decimal(height) * 2110
so_1_height_first =int(math.floor(float(so_1_height_first)))

so_1_height_end = Decimal(height) * 2260
so_1_height_end =int(math.floor(float(so_1_height_end)))

so_1 = img[so_1_height_first:so_1_height_end,so_1_width_first:so_1_width_end]


# so_1 = img[2100:2250,1110:1580] #質問1

############################################
# 社会質問2 +10
############################################
so_2_width_first = Decimal(width) * 1110
so_2_width_first =int(math.floor(float(so_2_width_first)))

so_2_width_end = Decimal(width) * 1580
so_2_width_end =int(math.floor(float(so_2_width_end)))

so_2_height_first = Decimal(height) * 2260
so_2_height_first =int(math.floor(float(so_2_height_first)))

so_2_height_end = Decimal(height) * 2410
so_2_height_end =int(math.floor(float(so_2_height_end)))

so_2 = img[so_2_height_first:so_2_height_end,so_2_width_first:so_2_width_end]

# so_2 = img[2250:2400,1110:1580] #質問2

############################################
# 社会質問3 +10
############################################
so_3_width_first = Decimal(width) * 1110
so_3_width_first =int(math.floor(float(so_3_width_first)))

so_3_width_end = Decimal(width) * 1580
so_3_width_end =int(math.floor(float(so_3_width_end)))

so_3_height_first = Decimal(height) * 2410
so_3_height_first =int(math.floor(float(so_3_height_first)))

so_3_height_end = Decimal(height) * 2560
so_3_height_end =int(math.floor(float(so_3_height_end)))

so_3 = img[so_3_height_first:so_3_height_end,so_3_width_first:so_3_width_end]

# so_3 = img[2400:2550,1110:1580] #質問3


############################################
# 社会質問4 +10
############################################
so_4_width_first = Decimal(width) * 1110
so_4_width_first =int(math.floor(float(so_4_width_first)))

so_4_width_end = Decimal(width) * 1580
so_4_width_end =int(math.floor(float(so_4_width_end)))

so_4_height_first = Decimal(height) * 2540
so_4_height_first =int(math.floor(float(so_4_height_first)))

so_4_height_end = Decimal(height) * 2690
so_4_height_end =int(math.floor(float(so_4_height_end)))

so_4 = img[so_4_height_first:so_4_height_end,so_4_width_first:so_4_width_end]


# so_4 = img[2530:2680,1110:1580] #質問4

############################################
# 社会質問5 +10
############################################
so_5_width_first = Decimal(width) * 1110
so_5_width_first =int(math.floor(float(so_5_width_first)))

so_5_width_end = Decimal(width) * 1580
so_5_width_end =int(math.floor(float(so_5_width_end)))

so_5_height_first = Decimal(height) * 2680
so_5_height_first =int(math.floor(float(so_5_height_first)))

so_5_height_end = Decimal(height) * 2840
so_5_height_end =int(math.floor(float(so_5_height_end)))

so_5 = img[so_5_height_first:so_5_height_end,so_5_width_first:so_5_width_end]

# so_5 = img[2670:2830,1110:1580] #質問5

############################################
# 社会質問6 +10
############################################
so_6_width_first = Decimal(width) * 1110
so_6_width_first =int(math.floor(float(so_6_width_first)))

so_6_width_end = Decimal(width) * 1580
so_6_width_end =int(math.floor(float(so_6_width_end)))

so_6_height_first = Decimal(height) * 2820
so_6_height_first =int(math.floor(float(so_6_height_first)))

so_6_height_end = Decimal(height) * 2980
so_6_height_end =int(math.floor(float(so_6_height_end)))

so_6 = img[so_6_height_first:so_6_height_end,so_6_width_first:so_6_width_end]

# so_6 = img[2810:2970,1110:1580] #質問6


############################################
# 社会質問7 +10
############################################
so_7_width_first = Decimal(width) * 1110
so_7_width_first =int(math.floor(float(so_7_width_first)))

so_7_width_end = Decimal(width) * 1580
so_7_width_end =int(math.floor(float(so_7_width_end)))

so_7_height_first = Decimal(height) * 2970
so_7_height_first =int(math.floor(float(so_7_height_first)))

so_7_height_end = Decimal(height) * 3130
so_7_height_end =int(math.floor(float(so_7_height_end)))

so_7 = img[so_7_height_first:so_7_height_end,so_7_width_first:so_7_width_end]

# so_7 = img[2960:3120,1110:1580] #質問7

##############################################
# その他　クラス　幅
##############################################
o_class_width_first = Decimal(width) * 1900
o_class_width_first =int(math.floor(float(o_class_width_first)))

o_class_width_end = Decimal(width) * 2450
o_class_width_end =int(math.floor(float(o_class_width_end)))

############################################
#その他　クラス　高さ
############################################
o_class_height_first = Decimal(height) * 1950
o_class_height_first =int(math.floor(float(o_class_height_first)))

o_class_height_end = Decimal(height) * 2130
o_class_height_end =int(math.floor(float(o_class_height_end)))

o_class = img[o_class_height_first:o_class_height_end,o_class_width_first:o_class_width_end]


# o_class = img[1950:2130,1900:2450] #その他クラス

############################################
# その他質問1 +10
############################################
o_1_width_first = Decimal(width) * 1900
o_1_width_first =int(math.floor(float(o_1_width_first)))

o_1_width_end = Decimal(width) * 2350
o_1_width_end =int(math.floor(float(o_1_width_end)))

o_1_height_first = Decimal(height) * 2100
o_1_height_first =int(math.floor(float(o_1_height_first)))

o_1_height_end = Decimal(height) * 2250
o_1_height_end =int(math.floor(float(o_1_height_end)))

o_1 = img[o_1_height_first:o_1_height_end,o_1_width_first:o_1_width_end]

# o_1 = img[2100:2250,1900:2350] #質問1


############################################
# その他質問2 +10
############################################
o_2_width_first = Decimal(width) * 1900
o_2_width_first =int(math.floor(float(o_2_width_first)))

o_2_width_end = Decimal(width) * 2350
o_2_width_end =int(math.floor(float(o_2_width_end)))

o_2_height_first = Decimal(height) * 2250
o_2_height_first =int(math.floor(float(o_2_height_first)))

o_2_height_end = Decimal(height) * 2400
o_2_height_end =int(math.floor(float(o_2_height_end)))

o_2 = img[o_2_height_first:o_2_height_end,o_2_width_first:o_2_width_end]

# o_2 = img[2250:2400,1900:2350] #質問2



############################################
# その他質問3 +10
############################################
o_3_width_first = Decimal(width) * 1900
o_3_width_first =int(math.floor(float(o_3_width_first)))

o_3_width_end = Decimal(width) * 2350
o_3_width_end =int(math.floor(float(o_3_width_end)))

o_3_height_first = Decimal(height) * 2400
o_3_height_first =int(math.floor(float(o_3_height_first)))

o_3_height_end = Decimal(height) * 2550
o_3_height_end =int(math.floor(float(o_3_height_end)))

o_3 = img[o_3_height_first:o_3_height_end,o_3_width_first:o_3_width_end]

# o_3 = img[2400:2550,1900:2350] #質問3


############################################
# その他質問4 +10
############################################
o_4_width_first = Decimal(width) * 1900
o_4_width_first =int(math.floor(float(o_4_width_first)))

o_4_width_end = Decimal(width) * 2350
o_4_width_end =int(math.floor(float(o_4_width_end)))

o_4_height_first = Decimal(height) * 2530
o_4_height_first =int(math.floor(float(o_4_height_first)))

o_4_height_end = Decimal(height) * 2680
o_4_height_end =int(math.floor(float(o_4_height_end)))

o_4 = img[o_4_height_first:o_4_height_end,o_4_width_first:o_4_width_end]


# o_4 = img[2530:2680,1900:2350] #質問4


############################################
# その他質問5 +10
############################################
o_5_width_first = Decimal(width) * 1900
o_5_width_first =int(math.floor(float(o_5_width_first)))

o_5_width_end = Decimal(width) * 2350
o_5_width_end =int(math.floor(float(o_5_width_end)))

o_5_height_first = Decimal(height) * 2670
o_5_height_first =int(math.floor(float(o_5_height_first)))

o_5_height_end = Decimal(height) * 2830
o_5_height_end =int(math.floor(float(o_5_height_end)))

o_5 = img[o_5_height_first:o_5_height_end,o_5_width_first:o_5_width_end]


# o_5 = img[2670:2830,1900:2350] #質問5

############################################
# その他質問6 +10
############################################
o_6_width_first = Decimal(width) * 1900
o_6_width_first =int(math.floor(float(o_6_width_first)))

o_6_width_end = Decimal(width) * 2350
o_6_width_end =int(math.floor(float(o_6_width_end)))

o_6_height_first = Decimal(height) * 2810
o_6_height_first =int(math.floor(float(o_6_height_first)))

o_6_height_end = Decimal(height) * 2970
o_6_height_end =int(math.floor(float(o_6_height_end)))

o_6 = img[o_6_height_first:o_6_height_end,o_6_width_first:o_6_width_end]

# o_6 = img[2810:2970,1900:2350] #質問6

############################################
# その他　質問７　
###########################################
o_7_width_first = Decimal(width) * 1900
o_7_width_first =int(math.floor(float(o_7_width_first)))

o_7_width_end = Decimal(width) * 2350
o_7_width_end =int(math.floor(float(o_7_width_end)))

o_7_height_first = Decimal(height) * 2960
o_7_height_first =int(math.floor(float(o_7_height_first)))

o_7_height_end = Decimal(height) * 3120
o_7_height_end =int(math.floor(float(o_7_height_end)))

o_7 = img[o_7_height_first:o_7_height_end,o_7_width_first:o_7_width_end]

# o_7 = img[2960:3120,1900:2350] #質問7


# school_year = img[500:660,370:1540] #学年
# school_year = cv2.cvtColor(school_year, cv2.COLOR_BGR2RGB)

# e_class = img[730:900,350:850] #英語クラス

# e_1 = img[900:1050,350:780] #質問1
# e_2 = img[1030:1180,350:780] #質問2
# e_3 = img[1180:1330,350:780] #質問3
# e_4 = img[1330:1480,350:780] #質問4
# e_5 = img[1460:1610,350:780] #質問5
# e_6 = img[1590:1740,350:780] #質問6
# e_7 = img[1740:1920,350:780] #質問7

# s_class = img[730:900,1110:1650] #理科クラス
# s_1 = img[900:1050,1110:1580] #質問1
# s_2 = img[1030:1180,1110:1580] #質問2
# s_3 = img[1180:1330,1110:1580] #質問3
# s_4 = img[1320:1470,1120:1580] #質問4
# s_5 = img[1460:1610,1110:1580] #質問5
# s_6 = img[1600:1740,1110:1580] #質問6
# s_7 = img[1740:1920,1110:1580] #質問7

# m_class = img[730:900,1900:2450] #数学クラス
# m_1 = img[900:1050,1900:2350] #質問1
# m_2 = img[1030:1180,1900:2350] #質問2
# m_3 = img[1180:1330,1900:2350] #質問3
# m_4 = img[1330:1480,1900:2350] #質問4
# m_5 = img[1460:1610,1900:2350] #質問5
# m_6 = img[1590:1740,1900:2350] #質問6
# m_7 = img[1740:1920,1900:2350] #質問7

# j_class = img[1950:2130,350:850] #国語クラス
# j_1 = img[2100:2250,350:780] #国語質問1
# j_2 = img[2250:2400,350:780] #国語質問2
# j_3 = img[2400:2550,350:780] #国語質問3
# j_4 = img[2530:2680,350:780] #国語質問4
# j_5 = img[2670:2830,350:780] #国語質問5
# j_6 = img[2810:2970,350:780] #国語質問6
# j_7 = img[2960:3120,350:780] #国語質問7

# so_class = img[1950:2130,1110:1650] #社会クラス
# so_1 = img[2100:2250,1110:1580] #質問1
# so_2 = img[2250:2400,1110:1580] #質問2
# so_3 = img[2400:2550,1110:1580] #質問3
# so_4 = img[2530:2680,1110:1580] #質問4
# so_5 = img[2670:2830,1110:1580] #質問5
# so_6 = img[2810:2970,1110:1580] #質問6
# so_7 = img[2960:3120,1110:1580] #質問7

# o_class = img[1950:2130,1900:2450] #その他クラス
# o_1 = img[2100:2250,1900:2350] #質問1
# o_2 = img[2250:2400,1900:2350] #質問2
# o_3 = img[2400:2550,1900:2350] #質問3
# o_4 = img[2530:2680,1900:2350] #質問4
# o_5 = img[2670:2830,1900:2350] #質問5
# o_6 = img[2810:2970,1900:2350] #質問6
# o_7 = img[2960:3120,1900:2350] #質問7

#アンケート番号
cv2.imwrite('/var/www/html/shinzemi/ancake/images/id.jpg', id)
# cv2.imwrite('/var/www/html/shinzemi/ancake/images/ancake_id.jpg', id)

# 学年
cv2.imwrite('/var/www/html/shinzemi/ancake/images/school_year.jpg', school_year)
# cv2.imwrite('/var/www/html/shinzemi/ancake/images/school_year_id.jpg', school_year)

# 英語セット
cv2.imwrite('/var/www/html/shinzemi/ancake/images/alphabet_id_1.jpg', e_class)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_1_1.jpg', e_1)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_2_1.jpg', e_2)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_3_1.jpg', e_3)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_4_1.jpg', e_4)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_5_1.jpg', e_5)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_6_1.jpg', e_6)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_7_1.jpg', e_7)

# # 理科セット
cv2.imwrite('/var/www/html/shinzemi/ancake/images/alphabet_id_2.jpg', s_class)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_1_2.jpg', s_1)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_2_2.jpg', s_2)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_3_2.jpg', s_3)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_4_2.jpg', s_4)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_5_2.jpg', s_5)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_6_2.jpg', s_6)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_7_2.jpg', s_7)

# # 数学セット
cv2.imwrite('/var/www/html/shinzemi/ancake/images/alphabet_id_3.jpg', m_class)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_1_3.jpg', m_1)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_2_3.jpg', m_2)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_3_3.jpg', m_3)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_4_3.jpg', m_4)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_5_3.jpg', m_5)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_6_3.jpg', m_6)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_7_3.jpg', m_7)

# 国語セット
cv2.imwrite('/var/www/html/shinzemi/ancake/images/alphabet_id_4.jpg', j_class)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_1_4.jpg', j_1)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_2_4.jpg', j_2)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_3_4.jpg', j_3)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_4_4.jpg', j_4)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_5_4.jpg', j_5)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_6_4.jpg', j_6)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_7_4.jpg', j_7)

# 社会セット
cv2.imwrite('/var/www/html/shinzemi/ancake/images/alphabet_id_5.jpg', so_class)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_1_5.jpg', so_1)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_2_5.jpg', so_2)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_3_5.jpg', so_3)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_4_5.jpg', so_4)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_5_5.jpg', so_5)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_6_5.jpg', so_6)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_7_5.jpg', so_7)

# その他セット
cv2.imwrite('/var/www/html/shinzemi/ancake/images/alphabet_id_6.jpg', o_class)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_1_6.jpg', o_1)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_2_6.jpg', o_2)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_3_6.jpg', o_3)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_4_6.jpg', o_4)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_5_6.jpg', o_5)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_6_6.jpg', o_6)
cv2.imwrite('/var/www/html/shinzemi/ancake/images/question_7_6.jpg', o_7)


import uupy
# import wupy_check
