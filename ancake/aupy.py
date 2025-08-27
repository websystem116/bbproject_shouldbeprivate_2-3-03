
import os
os.chdir('/var/www/html/shinzemi/ancake/') ###★★★
#os.chdir('/var/www/ancake/')


#import cupy
import chainer
from PIL import Image
import numpy as np
#import matplotlib.pyplot as plt


INPUT_WIDTH = 420 #32
INPUT_HEIGHT = 150 #32
INPUT_WIDTH_ABC = 550 #32
INPUT_HEIGHT_ABC = 200 #32
INPUT_WIDTH_GAKU = 1300 #1160 #32   128
INPUT_HEIGHT_GAKU = 200 #160 #32    36

def data_reshape(width_height_channel_image):
  image_array = np.array(width_height_channel_image)
  return image_array.transpose(2, 0, 1)

import cv2
#import matplotlib.pyplot as plt


import chainer
import chainer.functions as F
import chainer.links as L



from chainer import training,serializers,Chain,datasets,sequential,optimizers,iterators
import json


class CNN(Chain):
  # コンストラクタ
  def __init__(self):
    super(CNN, self).__init__()

    with self.init_scope():
      self.conv1 = L.Convolution2D(None, out_channels=32, ksize=3, stride=1, pad=1)
      self.conv2 = L.Convolution2D(in_channels=32, out_channels=64, ksize=3, stride=1, pad=1)
      self.conv3 = L.Convolution2D(in_channels=64, out_channels=128, ksize=3, stride=1, pad=1)
      self.conv4 = L.Convolution2D(in_channels=128, out_channels=256, ksize=3, stride=1, pad=1)
      self.layer1 = L.Linear(None, 1000)
      self.layer2 = L.Linear(1000, 7)
  #
  def __call__(self, input):
    func = F.max_pooling_2d(F.relu(self.conv1(input)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv2(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv3(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv4(func)), ksize=2, stride=2)
    func = F.dropout(F.relu(self.layer1(func)), ratio=0.80)
    func = self.layer2(func)
    return func
modelABC = L.Classifier(CNN())

from chainer import serializers
serializers.load_hdf5("./chainer-dogscatsABC-model.h5", modelABC)


##CNNを設定する　ニューラルネットワーク　設問判定用
class CNN(Chain):
  # コンストラクタ
  def __init__(self):
    super(CNN, self).__init__()

    with self.init_scope():
      self.conv1 = L.Convolution2D(None, out_channels=32, ksize=3, stride=1, pad=1)
      self.conv2 = L.Convolution2D(in_channels=32, out_channels=64, ksize=3, stride=1, pad=1)
      self.conv3 = L.Convolution2D(in_channels=64, out_channels=128, ksize=3, stride=1, pad=1)
      self.conv4 = L.Convolution2D(in_channels=128, out_channels=256, ksize=3, stride=1, pad=1)
      self.layer1 = L.Linear(None, 1000)
      self.layer2 = L.Linear(1000, 5) # 最後は５個のどれかに振り分け
  #
  def __call__(self, input):
    func = F.max_pooling_2d(F.relu(self.conv1(input)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv2(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv3(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv4(func)), ksize=2, stride=2)
    func = F.dropout(F.relu(self.layer1(func)), ratio=0.80)
    func = self.layer2(func)
    return func
model = L.Classifier(CNN())

#モデルのロード
from chainer import serializers
serializers.load_hdf5("./chainer-dogscats-model.h5", model)

####設問判定 End

####学年の学習モデルLoad
##CNNを設定する　ニューラルネットワーク　学年判定用
class CNN(Chain):
  # コンストラクタ
  def __init__(self):
    super(CNN, self).__init__()

    with self.init_scope():
      self.conv1 = L.Convolution2D(None, out_channels=32, ksize=3, stride=1, pad=1)
      self.conv2 = L.Convolution2D(in_channels=32, out_channels=64, ksize=3, stride=1, pad=1)
      self.conv3 = L.Convolution2D(in_channels=64, out_channels=128, ksize=3, stride=1, pad=1)
      self.conv4 = L.Convolution2D(in_channels=128, out_channels=256, ksize=3, stride=1, pad=1)
      self.layer1 = L.Linear(None, 1000)
      self.layer2 = L.Linear(1000, 14)#　最後は１３個のどれかに振り分け
  #
  def __call__(self, input):
    func = F.max_pooling_2d(F.relu(self.conv1(input)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv2(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv3(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv4(func)), ksize=2, stride=2)
    func = F.dropout(F.relu(self.layer1(func)), ratio=0.80)
    func = self.layer2(func)
    return func
modelGaku = L.Classifier(CNN())

#モデルのロード
from chainer import serializers
serializers.load_hdf5("./chainer-dogscatsGaku-model.h5", modelGaku)

###############################
#関数の定義　
#
def data_reshape(width_height_channel_image):
  image_array = np.array(width_height_channel_image)
  return image_array.transpose(2, 0, 1)

def convert_test_dataABC(image_file_path, size, show=False):#ABC判別用
  #INPUT_WIDTH = 128 #32 128　　エラーActual: 57600 != 6144　なら　１２８ｘ３６かどうかうたがう
      
  image = Image.open(image_file_path)
  result_image = image.resize((INPUT_WIDTH_ABC,INPUT_HEIGHT_ABC),Image.LANCZOS)

  # 画像データをChainerのConvolution2Dに使えるように整備します
  image = data_reshape(result_image)
  # 型をfloat32に変換します
  result = image.astype(np.float32)
  # 学習済みモデルに渡します
  result = modelABC.xp.asarray(result)
  # モデルに渡すデータフォーマットに変換します
  result = result[None, ...]
  return result
  
def convert_test_data(image_file_path, size, show=False):#設問判別用

  image = Image.open(image_file_path)
  result_image = image.resize((INPUT_WIDTH,INPUT_HEIGHT),Image.LANCZOS)

  # 画像データをChainerのConvolution2Dに使えるように整備します
  image = data_reshape(result_image)
  # 型をfloat32に変換します
  result = image.astype(np.float32)
  # 学習済みモデルに渡します
  result = model.xp.asarray(result)
  # モデルに渡すデータフォーマットに変換します
  result = result[None, ...]
  return result

  
def convert_test_dataGaku(image_file_path, size, show=False):#設問判別用
  image = Image.open(image_file_path)
  result_image = image.resize((INPUT_WIDTH_GAKU,INPUT_HEIGHT_GAKU),Image.LANCZOS)

  # 画像データをChainerのConvolution2Dに使えるように整備します
  image = data_reshape(result_image)
  # 型をfloat32に変換します
  result = image.astype(np.float32)
  # 学習済みモデルに渡します
  result = model.xp.asarray(result)
  # モデルに渡すデータフォーマットに変換します
  result = result[None, ...]
  return result
  
  
################アンケートID判定############### 
def check_ID( path ):
    import os
    os.environ["GOOGLE_APPLICATION_CREDENTIALS"] = "./kimura01-a1eecf5c15f4.json"
    import platform

    #対象画像の読み込み
    import sys
    args = sys.argv
    #image_file ="./images/" + args[1]
    image_file = path


    #点線除去フィルター処理
#    import cv2
    # 画像の読み込み
#    img = cv2.imread(image_file, 0)
#    img = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
#    img_gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY) 
    # 二値化(閾値100を超えた画素を255にする。) 5で点線がだいぶん薄くなる
#    ret, img_thresh = cv2.threshold(img, 5, 255, cv2.THRESH_BINARY)
    #保存する
#    cv2.imwrite(path, img_thresh)


    import io
    from google.cloud import vision
    client = vision.ImageAnnotatorClient()

    with io.open(image_file,'rb') as image_file:
        content = image_file.read()
    image = vision.Image(content=content)

    #APIに投げる
    response = client.document_text_detection(image=image)
    document = response.full_text_annotation

    mojiretu = ''

    for page in document.pages:
        for block in page.blocks:
            for paragraph in block.paragraphs:      
                for word in paragraph.words:
                    for symbol in word.symbols:
                        mojiretu = mojiretu + symbol.text   #配列　文字自体
    mojiretu  = mojiretu.replace('|','') #   |
    mojiretu  = mojiretu.replace('l','')    
    mojiretu  = mojiretu.replace(':','')     
    mojiretu  = mojiretu.replace(';','')      
    mojiretu  = mojiretu.replace('.','')        
    return(mojiretu)
  
  
  
################アンケートID判定 END###########  
  
  
#複数枚アンケート処理   origin_images　フォルダ内のimg
import glob
#files = glob.glob("./origin_images/*.jpg")
# files = glob.glob("/var/www/html/shinzemi/ancake/origin_images/*.jpg")
files = glob.glob('/var/www/html/shinzemi/storage/app/upFiles/*.jpg')

ancake_array =[]
for file in files:


#################★★★画像をちぎる処理#############　ENDは１３０４行目
#################★★★画像をちぎる処理#############　ENDは１３０４行目
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

    filename = file

    img = cv2.imread(filename)

    # imgの高さと幅を取得
    img_height, img_width = img.shape[:2]
    # 係数計算
    num = img_width / 2483
    # 切り上げ
    width = f'{num:.1f}'
    # 係数計算
    num2 = img_height / 3506
    height = f'{num2:.1f}'


    #####################################
    # 学年 
    #######################################
    school_year_width_first = Decimal(width) * 200 
    school_year_width_first = int(math.floor(float(school_year_width_first)))

    school_year_width_end = Decimal(width) * 1500 
    school_year_width_end = int(math.floor(float(school_year_width_end)))

    school_year_height_first = Decimal(height) * 400 
    school_year_height_first =int(math.floor(float(school_year_height_first)))

    school_year_height_end = Decimal(height) * 600 
    school_year_height_end = int(math.floor(float(school_year_height_end)))

    school_year = img[school_year_height_first:school_year_height_end,school_year_width_first:school_year_width_end]


    ############################################
    # 英語　質問１ heightがずれるから＋１０
    ############################################
    

    e_1_width_first = Decimal(width) * 330
    e_1_width_first =int(math.floor(float(e_1_width_first)))

    e_1_width_end = Decimal(width) * 750
    e_1_width_end =int(math.floor(float(e_1_width_end)))
    
    e_1_height_first = Decimal(height) * 800
    e_1_height_first =int(math.floor(float(e_1_height_first)))

    e_1_height_end = Decimal(height) * 950
    e_1_height_end =int(math.floor(float(e_1_height_end)))


    e_1 = img[e_1_height_first:e_1_height_end,e_1_width_first:e_1_width_end] #英語クラス

    ############################################
    # 英語　質問2 heightがずれるから＋１０
    ############################################
    e_2_width_first = Decimal(width) * 330
    e_2_width_first =int(math.floor(float(e_2_width_first)))

    e_2_width_end = Decimal(width) * 750
    e_2_width_end =int(math.floor(float(e_2_width_end)))

    e_2_height_first = Decimal(height) * 940
    e_2_height_first =int(math.floor(float(e_2_height_first)))

    e_2_height_end = Decimal(height) * 1090
    e_2_height_end =int(math.floor(float(e_2_height_end)))

    e_2 = img[e_2_height_first:e_2_height_end,e_2_width_first:e_2_width_end] #英語クラス

    ############################################
    # 英語　質問3 heightがずれるから＋１０
    ############################################
    e_3_width_first = Decimal(width) * 330
    e_3_width_first =int(math.floor(float(e_3_width_first)))

    e_3_width_end = Decimal(width) * 750
    e_3_width_end =int(math.floor(float(e_3_width_end)))

    e_3_height_first = Decimal(height) * 1090
    e_3_height_first =int(math.floor(float(e_3_height_first)))

    e_3_height_end = Decimal(height) * 1240
    e_3_height_end =int(math.floor(float(e_3_height_end)))

    e_3 = img[e_3_height_first:e_3_height_end,e_3_width_first:e_3_width_end]

    ############################################
    # 英語　質問4 heightがずれるから＋１０
    ############################################
    e_4_width_first = Decimal(width) * 330
    e_4_width_first =int(math.floor(float(e_4_width_first)))

    e_4_width_end = Decimal(width) * 750
    e_4_width_end =int(math.floor(float(e_4_width_end)))

    e_4_height_first = Decimal(height) * 1240
    e_4_height_first =int(math.floor(float(e_4_height_first)))

    e_4_height_end = Decimal(height) * 1390
    e_4_height_end =int(math.floor(float(e_4_height_end)))

    e_4 = img[e_4_height_first:e_4_height_end,e_4_width_first:e_4_width_end]

    ############################################
    # 英語　質問5 heightがずれるから＋１０
    ############################################
    e_5_width_first = Decimal(width) * 330
    e_5_width_first =int(math.floor(float(e_5_width_first)))

    e_5_width_end = Decimal(width) * 750
    e_5_width_end =int(math.floor(float(e_5_width_end)))

    e_5_height_first = Decimal(height) * 1390
    e_5_height_first =int(math.floor(float(e_5_height_first)))

    e_5_height_end = Decimal(height) * 1540
    e_5_height_end =int(math.floor(float(e_5_height_end)))

    e_5 = img[e_5_height_first:e_5_height_end,e_5_width_first:e_5_width_end]

    ############################################
    # 英語　質問6 heightがずれるから＋１０
    ############################################
    e_6_width_first = Decimal(width) * 330 #430幅
    e_6_width_first =int(math.floor(float(e_6_width_first)))

    e_6_width_end = Decimal(width) * 750
    e_6_width_end =int(math.floor(float(e_6_width_end)))

    e_6_height_first = Decimal(height) * 1520
    e_6_height_first =int(math.floor(float(e_6_height_first)))

    e_6_height_end = Decimal(height) * 1670
    e_6_height_end =int(math.floor(float(e_6_height_end)))

    e_6 = img[e_6_height_first:e_6_height_end,e_6_width_first:e_6_width_end]

    ############################################
    # 英語　質問7 heightがずれるから＋１０
    ############################################
    e_7_width_first = Decimal(width) * 330
    e_7_width_first =int(math.floor(float(e_7_width_first)))

    e_7_width_end = Decimal(width) * 750
    e_7_width_end =int(math.floor(float(e_7_width_end)))

    e_7_height_first = Decimal(height) * 1670
    e_7_height_first =int(math.floor(float(e_7_height_first)))

    e_7_height_end = Decimal(height) * 1820
    e_7_height_end =int(math.floor(float(e_7_height_end)))

    e_7 = img[e_7_height_first:e_7_height_end,e_7_width_first:e_7_width_end]


 
    ############################################
    # 理科質問1
    ############################################
    s_1_width_first = Decimal(width) * 1080
    s_1_width_first =int(math.floor(float(s_1_width_first)))

    s_1_width_end = Decimal(width) * 1500
    s_1_width_end =int(math.floor(float(s_1_width_end)))

    s_1_height_first = Decimal(height) * 790
    s_1_height_first =int(math.floor(float(s_1_height_first)))

    s_1_height_end = Decimal(height) * 940
    s_1_height_end =int(math.floor(float(s_1_height_end)))

    s_1 = img[s_1_height_first:s_1_height_end,s_1_width_first:s_1_width_end]

    ############################################
    # 理科質問2
    ############################################
    s_2_width_first = Decimal(width) * 1080
    s_2_width_first =int(math.floor(float(s_2_width_first)))

    s_2_width_end = Decimal(width) * 1500
    s_2_width_end =int(math.floor(float(s_2_width_end)))

    s_2_height_first = Decimal(height) * 940
    s_2_height_first =int(math.floor(float(s_2_height_first)))

    s_2_height_end = Decimal(height) * 1090
    s_2_height_end =int(math.floor(float(s_2_height_end)))

    s_2 = img[s_2_height_first:s_2_height_end,s_2_width_first:s_2_width_end]

    ############################################
    # 理科質問3
    ############################################
    s_3_width_first = Decimal(width) * 1080
    s_3_width_first =int(math.floor(float(s_3_width_first)))

    s_3_width_end = Decimal(width) * 1500
    s_3_width_end =int(math.floor(float(s_3_width_end)))

    s_3_height_first = Decimal(height) * 1090
    s_3_height_first =int(math.floor(float(s_3_height_first)))

    s_3_height_end = Decimal(height) * 1240
    s_3_height_end =int(math.floor(float(s_3_height_end)))

    s_3 = img[s_3_height_first:s_3_height_end,s_3_width_first:s_3_width_end]

    # s_3 = img[1180:1330,1110:1580] #質問3

    ############################################
    # 理科質問4
    ############################################
    s_4_width_first = Decimal(width) * 1080
    s_4_width_first =int(math.floor(float(s_4_width_first)))

    s_4_width_end = Decimal(width) * 1500
    s_4_width_end =int(math.floor(float(s_4_width_end)))

    s_4_height_first = Decimal(height) * 1240
    s_4_height_first =int(math.floor(float(s_4_height_first)))

    s_4_height_end = Decimal(height) * 1390
    s_4_height_end =int(math.floor(float(s_4_height_end)))

    s_4 = img[s_4_height_first:s_4_height_end,s_4_width_first:s_4_width_end]


    # s_4 = img[1330:1480,1110:1580] #質問4

    ############################################
    # 理科質問5
    ############################################
    s_5_width_first = Decimal(width) * 1080
    s_5_width_first =int(math.floor(float(s_5_width_first)))

    s_5_width_end = Decimal(width) * 1500
    s_5_width_end =int(math.floor(float(s_5_width_end)))

    s_5_height_first = Decimal(height) * 1370
    s_5_height_first =int(math.floor(float(s_5_height_first)))

    s_5_height_end = Decimal(height) * 1520
    s_5_height_end =int(math.floor(float(s_5_height_end)))

    s_5 = img[s_5_height_first:s_5_height_end,s_5_width_first:s_5_width_end]

    # s_5 = img[1460:1610,1110:1580] #質問5

    ############################################
    # 理科質問6
    ############################################
    s_6_width_first = Decimal(width) * 1080
    s_6_width_first =int(math.floor(float(s_6_width_first)))

    s_6_width_end = Decimal(width) * 1500
    s_6_width_end =int(math.floor(float(s_6_width_end)))

    s_6_height_first = Decimal(height) * 1520
    s_6_height_first =int(math.floor(float(s_6_height_first)))

    s_6_height_end = Decimal(height) * 1670
    s_6_height_end =int(math.floor(float(s_6_height_end)))

    s_6 = img[s_6_height_first:s_6_height_end,s_6_width_first:s_6_width_end]

    # s_6 = img[1590:1740,1110:1580] #質問6


    ############################################
    # 理科質問7
    ############################################
    s_7_width_first = Decimal(width) * 1080
    s_7_width_first =int(math.floor(float(s_7_width_first)))

    s_7_width_end = Decimal(width) * 1500
    s_7_width_end =int(math.floor(float(s_7_width_end)))

    s_7_height_first = Decimal(height) * 1670
    s_7_height_first =int(math.floor(float(s_7_height_first)))

    s_7_height_end = Decimal(height) * 1820
    s_7_height_end =int(math.floor(float(s_7_height_end)))

    s_7 = img[s_7_height_first:s_7_height_end,s_7_width_first:s_7_width_end]

    # s_7 = img[1740:1920,1110:1580] #質問7



    ############################################
    # 数学質問1
    ############################################
    m_1_width_first = Decimal(width) * 1830
    m_1_width_first =int(math.floor(float(m_1_width_first)))

    m_1_width_end = Decimal(width) * 2250
    m_1_width_end =int(math.floor(float(m_1_width_end)))

    m_1_height_first = Decimal(height) * 790
    m_1_height_first =int(math.floor(float(m_1_height_first)))

    m_1_height_end = Decimal(height) * 940
    m_1_height_end =int(math.floor(float(m_1_height_end)))

    m_1 = img[m_1_height_first:m_1_height_end,m_1_width_first:m_1_width_end]

    # m_1 = img[900:1050,1900:2350] #質問1

    ############################################
    # 数学質問2
    ############################################
    m_2_width_first = Decimal(width) * 1830
    m_2_width_first =int(math.floor(float(m_2_width_first)))

    m_2_width_end = Decimal(width) * 2250
    m_2_width_end =int(math.floor(float(m_2_width_end)))

    m_2_height_first = Decimal(height) * 940
    m_2_height_first =int(math.floor(float(m_2_height_first)))

    m_2_height_end = Decimal(height) * 1090
    m_2_height_end =int(math.floor(float(m_2_height_end)))

    m_2 = img[m_2_height_first:m_2_height_end,m_2_width_first:m_2_width_end]

    # m_2 = img[1030:1180,1900:2350] #質問2

    ############################################
    # 数学質問3
    ############################################
    m_3_width_first = Decimal(width) * 1830
    m_3_width_first =int(math.floor(float(m_3_width_first)))

    m_3_width_end = Decimal(width) * 2250
    m_3_width_end =int(math.floor(float(m_3_width_end)))

    m_3_height_first = Decimal(height) * 1090
    m_3_height_first =int(math.floor(float(m_3_height_first)))

    m_3_height_end = Decimal(height) * 1240
    m_3_height_end =int(math.floor(float(m_3_height_end)))

    m_3 = img[m_3_height_first:m_3_height_end,m_3_width_first:m_3_width_end]

    # m_3 = img[1180:1330,1900:2350] #質問3

    ############################################
    # 数学質問4
    ############################################
    m_4_width_first = Decimal(width) * 1830
    m_4_width_first =int(math.floor(float(m_4_width_first)))

    m_4_width_end = Decimal(width) * 2250
    m_4_width_end =int(math.floor(float(m_4_width_end)))

    m_4_height_first = Decimal(height) * 1240
    m_4_height_first =int(math.floor(float(m_4_height_first)))

    m_4_height_end = Decimal(height) * 1390
    m_4_height_end =int(math.floor(float(m_4_height_end)))

    m_4 = img[m_4_height_first:m_4_height_end,m_4_width_first:m_4_width_end]

    # m_4 = img[1330:1480,1900:2350] #質問4

    ############################################
    # 数学質問5
    ############################################
    m_5_width_first = Decimal(width) * 1830
    m_5_width_first =int(math.floor(float(m_5_width_first)))

    m_5_width_end = Decimal(width) * 2250
    m_5_width_end =int(math.floor(float(m_5_width_end)))

    m_5_height_first = Decimal(height) * 1370
    m_5_height_first =int(math.floor(float(m_5_height_first)))

    m_5_height_end = Decimal(height) * 1520
    m_5_height_end =int(math.floor(float(m_5_height_end)))

    m_5 = img[m_5_height_first:m_5_height_end,m_5_width_first:m_5_width_end]
    # m_5 = img[1460:1610,1900:2350] #質問5

    ############################################
    # 数学質問6
    ############################################
    m_6_width_first = Decimal(width) * 1830
    m_6_width_first =int(math.floor(float(m_6_width_first)))

    m_6_width_end = Decimal(width) * 2250
    m_6_width_end =int(math.floor(float(m_6_width_end)))

    m_6_height_first = Decimal(height) * 1520
    m_6_height_first =int(math.floor(float(m_6_height_first)))

    m_6_height_end = Decimal(height) * 1670
    m_6_height_end =int(math.floor(float(m_6_height_end)))

    m_6 = img[m_6_height_first:m_6_height_end,m_6_width_first:m_6_width_end]

    # m_6 = img[1590:1740,1900:2350] #質問6

    ############################################
    # 数学質問7
    ############################################
    m_7_width_first = Decimal(width) * 1830
    m_7_width_first =int(math.floor(float(m_7_width_first)))

    m_7_width_end = Decimal(width) * 2250
    m_7_width_end =int(math.floor(float(m_7_width_end)))

    m_7_height_first = Decimal(height) * 1670
    m_7_height_first =int(math.floor(float(m_7_height_first)))

    m_7_height_end = Decimal(height) * 1820
    m_7_height_end =int(math.floor(float(m_7_height_end)))

    m_7 = img[m_7_height_first:m_7_height_end,m_7_width_first:m_7_width_end]

    # m_7 = img[1740:1920,1900:2350] #質問7




    ############################################
    # 国語質問1 +10
    ############################################
    j_1_width_first = Decimal(width) * 330
    j_1_width_first =int(math.floor(float(j_1_width_first)))

    j_1_width_end = Decimal(width) * 750
    j_1_width_end =int(math.floor(float(j_1_width_end)))

    j_1_height_first = Decimal(height) * 2040
    j_1_height_first =int(math.floor(float(j_1_height_first)))

    j_1_height_end = Decimal(height) * 2190
    j_1_height_end =int(math.floor(float(j_1_height_end)))

    j_1 = img[j_1_height_first:j_1_height_end,j_1_width_first:j_1_width_end]


    # j_1 = img[2100:2250,350:780] #国語質問1


    ############################################
    # 国語質問2 +10
    ############################################
    j_2_width_first = Decimal(width) * 330
    j_2_width_first =int(math.floor(float(j_2_width_first)))

    j_2_width_end = Decimal(width) * 750
    j_2_width_end =int(math.floor(float(j_2_width_end)))

    j_2_height_first = Decimal(height) * 2190
    j_2_height_first =int(math.floor(float(j_2_height_first)))

    j_2_height_end = Decimal(height) * 2340
    j_2_height_end =int(math.floor(float(j_2_height_end)))

    j_2 = img[j_2_height_first:j_2_height_end,j_2_width_first:j_2_width_end]


    # j_2 = img[2250:2400,350:780] #国語質問2

    ############################################
    # 国語質問3 +10
    ############################################
    j_3_width_first = Decimal(width) * 330
    j_3_width_first =int(math.floor(float(j_3_width_first)))

    j_3_width_end = Decimal(width) * 750
    j_3_width_end =int(math.floor(float(j_3_width_end)))

    j_3_height_first = Decimal(height) * 2330
    j_3_height_first =int(math.floor(float(j_3_height_first)))

    j_3_height_end = Decimal(height) * 2480
    j_3_height_end =int(math.floor(float(j_3_height_end)))

    j_3 = img[j_3_height_first:j_3_height_end,j_3_width_first:j_3_width_end]

    # j_3 = img[2400:2550,350:780] #国語質問3

    ############################################
    # 国語質問4 +10
    ############################################
    j_4_width_first = Decimal(width) * 330
    j_4_width_first =int(math.floor(float(j_4_width_first)))

    j_4_width_end = Decimal(width) * 750
    j_4_width_end =int(math.floor(float(j_4_width_end)))

    j_4_height_first = Decimal(height) * 2480
    j_4_height_first =int(math.floor(float(j_4_height_first)))

    j_4_height_end = Decimal(height) * 2630
    j_4_height_end =int(math.floor(float(j_4_height_end)))

    j_4 = img[j_4_height_first:j_4_height_end,j_4_width_first:j_4_width_end]

    # j_4 = img[2530:2680,350:780] #国語質問4

    ############################################
    # 国語質問5 +10
    ############################################
    j_5_width_first = Decimal(width) * 330
    j_5_width_first =int(math.floor(float(j_5_width_first)))

    j_5_width_end = Decimal(width) * 750
    j_5_width_end =int(math.floor(float(j_5_width_end)))

    j_5_height_first = Decimal(height) * 2620
    j_5_height_first =int(math.floor(float(j_5_height_first)))

    j_5_height_end = Decimal(height) * 2770
    j_5_height_end =int(math.floor(float(j_5_height_end)))

    j_5 = img[j_5_height_first:j_5_height_end,j_5_width_first:j_5_width_end]


    # j_5 = img[2670:2830,350:780] #国語質問5


    ############################################
    # 国語質問6 +10
    ############################################
    j_6_width_first = Decimal(width) * 330
    j_6_width_first =int(math.floor(float(j_6_width_first)))

    j_6_width_end = Decimal(width) * 750
    j_6_width_end =int(math.floor(float(j_6_width_end)))

    j_6_height_first = Decimal(height) * 2760
    j_6_height_first =int(math.floor(float(j_6_height_first)))

    j_6_height_end = Decimal(height) * 2910
    j_6_height_end =int(math.floor(float(j_6_height_end)))

    j_6 = img[j_6_height_first:j_6_height_end,j_6_width_first:j_6_width_end]


    # j_6 = img[2810:2970,350:780] #国語質問6


    ############################################
    # 国語質問7 +10
    ############################################
    j_7_width_first = Decimal(width) * 330
    j_7_width_first =int(math.floor(float(j_7_width_first)))

    j_7_width_end = Decimal(width) * 750
    j_7_width_end =int(math.floor(float(j_7_width_end)))

    j_7_height_first = Decimal(height) * 2910
    j_7_height_first =int(math.floor(float(j_7_height_first)))

    j_7_height_end = Decimal(height) * 3060
    j_7_height_end =int(math.floor(float(j_7_height_end)))

    j_7 = img[j_7_height_first:j_7_height_end,j_7_width_first:j_7_width_end]

    # j_7 = img[2960:3120,350:780] #国語質問7



    ############################################
    # 社会質問1 +10
    ############################################
    so_1_width_first = Decimal(width) * 1080 #
    so_1_width_first =int(math.floor(float(so_1_width_first)))

    so_1_width_end = Decimal(width) * 1500
    so_1_width_end =int(math.floor(float(so_1_width_end)))

    so_1_height_first = Decimal(height) * 2030
    so_1_height_first =int(math.floor(float(so_1_height_first)))

    so_1_height_end = Decimal(height) * 2180
    so_1_height_end =int(math.floor(float(so_1_height_end)))

    so_1 = img[so_1_height_first:so_1_height_end,so_1_width_first:so_1_width_end]


    # so_1 = img[2100:2250,1110:1580] #質問1

    ############################################
    # 社会質問2 +10
    ############################################
    so_2_width_first = Decimal(width) * 1080
    so_2_width_first =int(math.floor(float(so_2_width_first)))

    so_2_width_end = Decimal(width) * 1500
    so_2_width_end =int(math.floor(float(so_2_width_end)))

    so_2_height_first = Decimal(height) * 2180
    so_2_height_first =int(math.floor(float(so_2_height_first)))

    so_2_height_end = Decimal(height) * 2330
    so_2_height_end =int(math.floor(float(so_2_height_end)))

    so_2 = img[so_2_height_first:so_2_height_end,so_2_width_first:so_2_width_end]

    # so_2 = img[2250:2400,1110:1580] #質問2

    ############################################
    # 社会質問3 +10
    ############################################
    so_3_width_first = Decimal(width) * 1080
    so_3_width_first =int(math.floor(float(so_3_width_first)))

    so_3_width_end = Decimal(width) * 1500
    so_3_width_end =int(math.floor(float(so_3_width_end)))

    so_3_height_first = Decimal(height) * 2330
    so_3_height_first =int(math.floor(float(so_3_height_first)))

    so_3_height_end = Decimal(height) * 2480
    so_3_height_end =int(math.floor(float(so_3_height_end)))

    so_3 = img[so_3_height_first:so_3_height_end,so_3_width_first:so_3_width_end]

    # so_3 = img[2400:2550,1110:1580] #質問3


    ############################################
    # 社会質問4 +10
    ############################################
    so_4_width_first = Decimal(width) * 1080
    so_4_width_first =int(math.floor(float(so_4_width_first)))

    so_4_width_end = Decimal(width) * 1500
    so_4_width_end =int(math.floor(float(so_4_width_end)))

    so_4_height_first = Decimal(height) * 2460
    so_4_height_first =int(math.floor(float(so_4_height_first)))

    so_4_height_end = Decimal(height) * 2610
    so_4_height_end =int(math.floor(float(so_4_height_end)))

    so_4 = img[so_4_height_first:so_4_height_end,so_4_width_first:so_4_width_end]


    # so_4 = img[2530:2680,1110:1580] #質問4

    ############################################
    # 社会質問5 +10
    ############################################
    so_5_width_first = Decimal(width) * 1080
    so_5_width_first =int(math.floor(float(so_5_width_first)))

    so_5_width_end = Decimal(width) * 1500
    so_5_width_end =int(math.floor(float(so_5_width_end)))

    so_5_height_first = Decimal(height) * 2620
    so_5_height_first =int(math.floor(float(so_5_height_first)))

    so_5_height_end = Decimal(height) * 2770
    so_5_height_end =int(math.floor(float(so_5_height_end)))

    so_5 = img[so_5_height_first:so_5_height_end,so_5_width_first:so_5_width_end]

    # so_5 = img[2670:2830,1110:1580] #質問5

    ############################################
    # 社会質問6 +10
    ############################################
    so_6_width_first = Decimal(width) * 1080
    so_6_width_first =int(math.floor(float(so_6_width_first)))

    so_6_width_end = Decimal(width) * 1500
    so_6_width_end =int(math.floor(float(so_6_width_end)))

    so_6_height_first = Decimal(height) * 2760
    so_6_height_first =int(math.floor(float(so_6_height_first)))

    so_6_height_end = Decimal(height) * 2910
    so_6_height_end =int(math.floor(float(so_6_height_end)))

    so_6 = img[so_6_height_first:so_6_height_end,so_6_width_first:so_6_width_end]

    # so_6 = img[2810:2970,1110:1580] #質問6


    ############################################
    # 社会質問7 +10
    ############################################
    so_7_width_first = Decimal(width) * 1080
    so_7_width_first =int(math.floor(float(so_7_width_first)))

    so_7_width_end = Decimal(width) * 1500
    so_7_width_end =int(math.floor(float(so_7_width_end)))

    so_7_height_first = Decimal(height) * 2910
    so_7_height_first =int(math.floor(float(so_7_height_first)))

    so_7_height_end = Decimal(height) * 3060
    so_7_height_end =int(math.floor(float(so_7_height_end)))

    so_7 = img[so_7_height_first:so_7_height_end,so_7_width_first:so_7_width_end]

    # so_7 = img[2960:3120,1110:1580] #質問7



    ############################################
    # その他質問1 +10
    ############################################
    o_1_width_first = Decimal(width) * 1830
    o_1_width_first =int(math.floor(float(o_1_width_first)))

    o_1_width_end = Decimal(width) * 2250
    o_1_width_end =int(math.floor(float(o_1_width_end)))

    o_1_height_first = Decimal(height) * 2020
    o_1_height_first =int(math.floor(float(o_1_height_first)))

    o_1_height_end = Decimal(height) * 2170
    o_1_height_end =int(math.floor(float(o_1_height_end)))

    o_1 = img[o_1_height_first:o_1_height_end,o_1_width_first:o_1_width_end]

    # o_1 = img[2100:2250,1900:2350] #質問1


    ############################################
    # その他質問2 +10
    ############################################
    o_2_width_first = Decimal(width) * 1830
    o_2_width_first =int(math.floor(float(o_2_width_first)))

    o_2_width_end = Decimal(width) * 2250
    o_2_width_end =int(math.floor(float(o_2_width_end)))

    o_2_height_first = Decimal(height) * 2170
    o_2_height_first =int(math.floor(float(o_2_height_first)))

    o_2_height_end = Decimal(height) * 2320
    o_2_height_end =int(math.floor(float(o_2_height_end)))

    o_2 = img[o_2_height_first:o_2_height_end,o_2_width_first:o_2_width_end]

    # o_2 = img[2250:2400,1900:2350] #質問2



    ############################################
    # その他質問3 +10
    ############################################
    o_3_width_first = Decimal(width) * 1830
    o_3_width_first =int(math.floor(float(o_3_width_first)))

    o_3_width_end = Decimal(width) * 2250
    o_3_width_end =int(math.floor(float(o_3_width_end)))

    o_3_height_first = Decimal(height) * 2320
    o_3_height_first =int(math.floor(float(o_3_height_first)))

    o_3_height_end = Decimal(height) * 2470
    o_3_height_end =int(math.floor(float(o_3_height_end)))

    o_3 = img[o_3_height_first:o_3_height_end,o_3_width_first:o_3_width_end]

    # o_3 = img[2400:2550,1900:2350] #質問3


    ############################################
    # その他質問4 +10
    ############################################
    o_4_width_first = Decimal(width) * 1830
    o_4_width_first =int(math.floor(float(o_4_width_first)))

    o_4_width_end = Decimal(width) * 2250
    o_4_width_end =int(math.floor(float(o_4_width_end)))

    o_4_height_first = Decimal(height) * 2470
    o_4_height_first =int(math.floor(float(o_4_height_first)))

    o_4_height_end = Decimal(height) * 2620
    o_4_height_end =int(math.floor(float(o_4_height_end)))

    o_4 = img[o_4_height_first:o_4_height_end,o_4_width_first:o_4_width_end]


    # o_4 = img[2530:2680,1900:2350] #質問4


    ############################################
    # その他質問5 +10
    ############################################
    o_5_width_first = Decimal(width) * 1830
    o_5_width_first =int(math.floor(float(o_5_width_first)))

    o_5_width_end = Decimal(width) * 2250
    o_5_width_end =int(math.floor(float(o_5_width_end)))

    o_5_height_first = Decimal(height) * 2620
    o_5_height_first =int(math.floor(float(o_5_height_first)))

    o_5_height_end = Decimal(height) * 2770
    o_5_height_end =int(math.floor(float(o_5_height_end)))

    o_5 = img[o_5_height_first:o_5_height_end,o_5_width_first:o_5_width_end]


    # o_5 = img[2670:2830,1900:2350] #質問5

    ############################################
    # その他質問6 +10
    ############################################
    o_6_width_first = Decimal(width) * 1830
    o_6_width_first =int(math.floor(float(o_6_width_first)))

    o_6_width_end = Decimal(width) * 2250
    o_6_width_end =int(math.floor(float(o_6_width_end)))

    o_6_height_first = Decimal(height) * 2770
    o_6_height_first =int(math.floor(float(o_6_height_first)))

    o_6_height_end = Decimal(height) * 2920
    o_6_height_end =int(math.floor(float(o_6_height_end)))

    o_6 = img[o_6_height_first:o_6_height_end,o_6_width_first:o_6_width_end]

    # o_6 = img[2810:2970,1900:2350] #質問6

    ############################################
    # その他　質問７　
    ###########################################
    o_7_width_first = Decimal(width) * 1830
    o_7_width_first =int(math.floor(float(o_7_width_first)))

    o_7_width_end = Decimal(width) * 2250
    o_7_width_end =int(math.floor(float(o_7_width_end)))

    o_7_height_first = Decimal(height) * 2900
    o_7_height_first =int(math.floor(float(o_7_height_first)))

    o_7_height_end = Decimal(height) * 3050
    o_7_height_end =int(math.floor(float(o_7_height_end)))

    o_7 = img[o_7_height_first:o_7_height_end,o_7_width_first:o_7_width_end]




   ##############################################
    # 英語クラス
    ###############################################
    e_class_width_first = Decimal(width) * 310
    e_class_width_first =int(math.floor(float(e_class_width_first)))

    e_class_width_end = Decimal(width) * 860
    e_class_width_end =int(math.floor(float(e_class_width_end)))

    e_class_height_first = Decimal(height) * 600
    e_class_height_first =int(math.floor(float(e_class_height_first)))

    e_class_height_end = Decimal(height) * 800
    e_class_height_end =int(math.floor(float(e_class_height_end)))

    e_class = img[e_class_height_first:e_class_height_end,e_class_width_first:e_class_width_end] #英語クラス

    ############################################
    # 理科クラス　
    ############################################
    s_class_width_first = Decimal(width) * 1050
    s_class_width_first =int(math.floor(float(s_class_width_first)))

    s_class_width_end = Decimal(width) * 1600
    s_class_width_end =int(math.floor(float(s_class_width_end)))

    s_class_height_first = Decimal(height) * 600
    s_class_height_first =int(math.floor(float(s_class_height_first)))

    s_class_height_end = Decimal(height) * 800
    s_class_height_end =int(math.floor(float(s_class_height_end)))

    s_class = img[s_class_height_first:s_class_height_end,s_class_width_first:s_class_width_end]

    ############################################
    # 数学クラス　
    ############################################
    m_class_width_first = Decimal(width) * 1800
    m_class_width_first =int(math.floor(float(m_class_width_first)))

    m_class_width_end = Decimal(width) * 2350
    m_class_width_end =int(math.floor(float(m_class_width_end)))

    m_class_height_first = Decimal(height) * 600
    m_class_height_first =int(math.floor(float(m_class_height_first)))

    m_class_height_end = Decimal(height) * 800
    m_class_height_end =int(math.floor(float(m_class_height_end)))

    m_class = img[m_class_height_first:m_class_height_end,m_class_width_first:m_class_width_end]

    # m_class = img[730:900,1900:2450] #数学クラス


    ############################################
    # 国語クラス　
    ############################################
    j_class_width_first = Decimal(width) * 310
    j_class_width_first =int(math.floor(float(j_class_width_first)))

    j_class_width_end = Decimal(width) * 860
    j_class_width_end =int(math.floor(float(j_class_width_end)))

    j_class_height_first = Decimal(height) * 1840
    j_class_height_first =int(math.floor(float(j_class_height_first)))

    j_class_height_end = Decimal(height) * 2040
    j_class_height_end =int(math.floor(float(j_class_height_end)))

    j_class = img[j_class_height_first:j_class_height_end,j_class_width_first:j_class_width_end]

    # j_class = img[1950:2130,350:850] #国語クラス



    ############################################
    # 社会クラス　
    ############################################
    so_class_width_first = Decimal(width) * 1050
    so_class_width_first =int(math.floor(float(so_class_width_first)))

    so_class_width_end = Decimal(width) * 1600
    so_class_width_end =int(math.floor(float(so_class_width_end)))

    so_class_height_first = Decimal(height) * 1840
    so_class_height_first =int(math.floor(float(so_class_height_first)))

    so_class_height_end = Decimal(height) * 2040
    so_class_height_end =int(math.floor(float(so_class_height_end)))

    so_class = img[so_class_height_first:so_class_height_end,so_class_width_first:so_class_width_end]


    # so_class = img[1950:2130,1110:1650] #社会クラス

    ##############################################
    # その他　クラス　幅
    ##############################################
    o_class_width_first = Decimal(width) * 1800
    o_class_width_first =int(math.floor(float(o_class_width_first)))

    o_class_width_end = Decimal(width) * 2350
    o_class_width_end =int(math.floor(float(o_class_width_end)))

    o_class_height_first = Decimal(height) * 1840
    o_class_height_first =int(math.floor(float(o_class_height_first)))

    o_class_height_end = Decimal(height) * 2040
    o_class_height_end =int(math.floor(float(o_class_height_end)))

    o_class = img[o_class_height_first:o_class_height_end,o_class_width_first:o_class_width_end]


    # o_class = img[1950:2130,1900:2450] #その他クラス


    ########################################
    # アンケート番号
    ########################################
    id_width_first = Decimal(width) * 1800
    id_width_first =int(math.floor(float(id_width_first)))

    id_width_end = Decimal(width) * 2250
    id_width_end =int(math.floor(float(id_width_end)))

    id_height_first = Decimal(height) * 320
    id_height_first =int(math.floor(float(id_height_first)))

    id_height_end = Decimal(height) * 500
    id_height_end =int(math.floor(float(id_height_end)))

    id = img[id_height_first:id_height_end,id_width_first:id_width_end] 


    #アンケート番号
    cv2.imwrite('./images/id.jpg', id)
    # cv2.imwrite('./images/ancake_id.jpg', id)

    # 学年
    cv2.imwrite('./images/school_year.jpg', school_year)
    # cv2.imwrite('./images/school_year_id.jpg', school_year)

    # 英語セット
    cv2.imwrite('./images/class_1.jpg', e_class)
    cv2.imwrite('./images/question_1_1.jpg', e_1)
    cv2.imwrite('./images/question_2_1.jpg', e_2)
    cv2.imwrite('./images/question_3_1.jpg', e_3)
    cv2.imwrite('./images/question_4_1.jpg', e_4)
    cv2.imwrite('./images/question_5_1.jpg', e_5)
    cv2.imwrite('./images/question_6_1.jpg', e_6)
    cv2.imwrite('./images/question_7_1.jpg', e_7)

    # # 理科セット
    cv2.imwrite('./images/class_2.jpg', s_class)
    cv2.imwrite('./images/question_1_2.jpg', s_1)
    cv2.imwrite('./images/question_2_2.jpg', s_2)
    cv2.imwrite('./images/question_3_2.jpg', s_3)
    cv2.imwrite('./images/question_4_2.jpg', s_4)
    cv2.imwrite('./images/question_5_2.jpg', s_5)
    cv2.imwrite('./images/question_6_2.jpg', s_6)
    cv2.imwrite('./images/question_7_2.jpg', s_7)

    # # 数学セット
    cv2.imwrite('./images/class_3.jpg', m_class)
    cv2.imwrite('./images/question_1_3.jpg', m_1)
    cv2.imwrite('./images/question_2_3.jpg', m_2)
    cv2.imwrite('./images/question_3_3.jpg', m_3)
    cv2.imwrite('./images/question_4_3.jpg', m_4)
    cv2.imwrite('./images/question_5_3.jpg', m_5)
    cv2.imwrite('./images/question_6_3.jpg', m_6)
    cv2.imwrite('./images/question_7_3.jpg', m_7)

    # 国語セット
    cv2.imwrite('./images/class_4.jpg', j_class)
    cv2.imwrite('./images/question_1_4.jpg', j_1)
    cv2.imwrite('./images/question_2_4.jpg', j_2)
    cv2.imwrite('./images/question_3_4.jpg', j_3)
    cv2.imwrite('./images/question_4_4.jpg', j_4)
    cv2.imwrite('./images/question_5_4.jpg', j_5)
    cv2.imwrite('./images/question_6_4.jpg', j_6)
    cv2.imwrite('./images/question_7_4.jpg', j_7)

    # 社会セット
    cv2.imwrite('./images/class_5.jpg', so_class)
    cv2.imwrite('./images/question_1_5.jpg', so_1)
    cv2.imwrite('./images/question_2_5.jpg', so_2)
    cv2.imwrite('./images/question_3_5.jpg', so_3)
    cv2.imwrite('./images/question_4_5.jpg', so_4)
    cv2.imwrite('./images/question_5_5.jpg', so_5)
    cv2.imwrite('./images/question_6_5.jpg', so_6)
    cv2.imwrite('./images/question_7_5.jpg', so_7)

    # その他セット
    cv2.imwrite('./images/class_6.jpg', o_class)
    cv2.imwrite('./images/question_1_6.jpg', o_1)
    cv2.imwrite('./images/question_2_6.jpg', o_2)
    cv2.imwrite('./images/question_3_6.jpg', o_3)
    cv2.imwrite('./images/question_4_6.jpg', o_4)
    cv2.imwrite('./images/question_5_6.jpg', o_5)
    cv2.imwrite('./images/question_6_6.jpg', o_6)
    cv2.imwrite('./images/question_7_6.jpg', o_7)

#################★★★画像をちぎる処理#############

    ########　アンケート画像判定処理　imagesフォルダにある画像名を全部、配列にいれる。

    from chainer.cuda import to_cpu
    retval = []
    id = ''
    school = ''

    import glob
    test_image_url_array =[]
    files = glob.glob("./images/*.jpg")#正規表現　[a-z_]-\d数字指定か文字列指定
    # files = glob.glob("./images/*.jpg")#正規表現　[a-z_]-\d数字指定か文字列指定
    for file in files:
        if 'id' in file:#アンケードIDを識別
            id = file
        elif 'school' in file:#学年判定 ファイル名にschoolってはいってるかどうか
            school = file
        else:
            test_image_url_array += [file]


    teacher_label = check_ID( id )
    id_name = id.replace('./images/','').replace('.jpg','')#ファイル名部分のみにする
    retval.append( {id_name:teacher_label} )  # アンケートIDの値


    teacher_label = school
    school_name = school.replace('./images/','').replace('.jpg','')#ファイル名部分のみにする
    school_data= convert_test_dataGaku(school, (1160, 160))
    with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
        teacher_labels = modelGaku.predictor(school_data)
        teacher_labels = to_cpu(teacher_labels.array)
        teacher_label = teacher_labels.argmax(axis=1)[0]
        teacher_label = teacher_label.astype(np.object)
        retval.append( {school_name:teacher_label} )  # 学年の値

    i = 0
    for test_image_url in test_image_url_array:
        image_name = test_image_url.replace('./images/','').replace('.jpg','')#ファイル名部分のみにする

        if 'class' in image_name:#ABCDE判別 ファイル名にclassってはいってるかどうか
            test_data= convert_test_dataABC(test_image_url, (INPUT_WIDTH_ABC, INPUT_HEIGHT_ABC))
            with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
              teacher_labels = modelABC.predictor(test_data)
              teacher_labels = to_cpu(teacher_labels.array)
              teacher_label = teacher_labels.argmax(axis=1)[0]
              
              if teacher_label == 0:
                teacher_label = teacher_label.astype(np.object)
                retval.append( {image_name:teacher_label} ) # レ点なし
                # retval.append( {image_name:'0'} ) # レ点なし
              elif teacher_label == 1:
                teacher_label = teacher_label.astype(np.object)
                # retval.append( {image_name:'A'} ) # 1にレ点あり
                retval.append( {image_name:teacher_label} ) # 1にレ点あり
              elif teacher_label == 2:
                teacher_label = teacher_label.astype(np.object)

                retval.append( {image_name:teacher_label} ) # 2にレ点あり
                # retval.append( {image_name:'B'} ) # 2にレ点あり
              elif teacher_label == 3:
                teacher_label = teacher_label.astype(np.object)

                retval.append( {image_name:teacher_label} ) # 3にレ点あり
                # retval.append( {image_name:'C'} ) # 3にレ点あり
              elif teacher_label == 4:
                teacher_label = teacher_label.astype(np.object)

                retval.append( {image_name:teacher_label} ) # 4にレ点あり
                # retval.append( {image_name:'D'} ) # 4にレ点あり
              elif teacher_label == 5:
                teacher_label = teacher_label.astype(np.object)

                retval.append( {image_name:teacher_label} ) # 5にレ点あり
                # retval.append( {image_name:'E'} ) # 5にレ点あり
              else :
                teacher_label = teacher_label.astype(np.object)

                retval.append( {image_name:''}) # 
                
        elif 'question' in image_name:#ABCDE判別 ファイル名にclassってはいってるかどうか
        
            test_data= convert_test_data(test_image_url, (INPUT_WIDTH, INPUT_HEIGHT))
            with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
              teacher_labels = model.predictor(test_data)
              teacher_labels = to_cpu(teacher_labels.array)
              teacher_label = teacher_labels.argmax(axis=1)[0]
              
              teacher_label = teacher_label.astype(np.object)
              retval.append({image_name:teacher_label}) # レ点なし
        
        i = i + 1
    ancake_array.append(retval)

#複数枚アンケート処理  End 
ancake_array_json = json.dumps(ancake_array)
print(ancake_array_json)

