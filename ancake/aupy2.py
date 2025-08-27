
import os
os.chdir('/var/www/html/shinzemi/ancake/') ###★★★

################アンケートID判定 END###########  

import sys
args = sys.argv

#複数枚アンケート処理   origin_images　フォルダ内のimg
import glob
files = glob.glob('/var/www/html/shinzemi/storage/app/upFiles/*.jpg')

# var/www/html/shinzemi/ancake/test内のファイルを削除()
test_files = glob.glob('/var/www/html/shinzemi/ancake/test/*')
for f in test_files:
    os.remove(f)


########################################
# 解析処理
########################################

#import cupy
import chainer
from PIL import Image
import numpy as np
#import matplotlib.pyplot as plt

INPUT_WIDTH = 420 #32
INPUT_HEIGHT = 150 #32
INPUT_WIDTH_ABC = 550 #32
INPUT_HEIGHT_ABC = 200 #32
INPUT_WIDTH_GAKU = 1300 #580     #1160 #32   128
INPUT_HEIGHT_GAKU = 200 #80 #160 #32    36

def data_reshape(width_height_channel_image):
  image_array = np.array(width_height_channel_image)
  return image_array.transpose(2, 0, 1)

import cv2
#import matplotlib.pyplot as plt


import chainer
import chainer.functions as F
import chainer.links as L

from chainer import training,serializers,Chain,datasets,sequential,optimizers,iterators

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
      self.layer2 = L.Linear(1000, 5)#　最後は５個のどれかに振り分け
  
  def __call__(self, input):
    func = F.max_pooling_2d(F.relu(self.conv1(input)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv2(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv3(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv4(func)), ksize=2, stride=2)
    func = F.dropout(F.relu(self.layer1(func)), ratio=0.80)
    func = self.layer2(func)
    return func

model = L.Classifier(CNN())
#model.to_gpu(GPU_ID) #時間かかった　GoogleColab ランタイム　ランタイプのタイプでGPU

#モデルのロード
from chainer import serializers
#serializers.save_hdf5("/content/drive/MyDrive/Colab/ancake/chainer-dogscats-model.h5", model)
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
      self.layer2 = L.Linear(1000, 4)#　最後は１３個のどれかに振り分け
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
import json

###############################
#関数の定義
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
def check_IDPre( path ):
    from PIL import Image
    import pyocr
    # OCRエンジンを取得
    engines = pyocr.get_available_tools()
    engine = engines[0]
    # 画像の文字を読み込む
    box_builder= pyocr.builders.WordBoxBuilder(tesseract_layout=6)
    txt = engine.image_to_string(Image.open('./images/id.jpg'), lang="eng",builder=box_builder)
    for listdata in txt:
            content = listdata.content
            position = str( listdata.position )#((107, 75), (327, 133))

    bbb = position.replace('(', '')
    bbb = bbb.replace(')', '')
    bbb = bbb.replace(','," ")
    bbb = bbb.split();#['107', '75', '327', '133']
    #bbb = bbb.append('100')
    return(bbb,content)
    
def check_ID( path ):
  
    from PIL import Image
    import pyocr

    # OCRエンジンを取得
    engines = pyocr.get_available_tools()
    engine = engines[0]

    # 画像の文字を読み込む
    box_builder= pyocr.builders.WordBoxBuilder(tesseract_layout=6)
    txt = engine.image_to_string(Image.open('./images/id.jpg'), lang="eng",builder=box_builder)
    #print(txt) # 「Test Message」が出力される
    for listdata in txt:
            #print(listdata)
            content = listdata.content
            #position = listdata.position
    return(content)
################アンケートID判定 END###########  

import sys
args = sys.argv

#複数枚アンケート処理   origin_images　フォルダ内のimg
import glob
files = glob.glob('/var/www/html/shinzemi/storage/app/upFiles/*.jpg')

# var/www/html/shinzemi/ancake/test内のファイルを削除()
test_files = glob.glob('/var/www/html/shinzemi/ancake/test/*')
for f in test_files:
    os.remove(f)

# var/www/html/shinzemi/storage/app/kobetsu内のファイルを削除()
kobetsu_files = glob.glob('/var/www/html/shinzemi/storage/app/public/kobetsu/*.jpg')
for f in kobetsu_files:
    os.remove(f)

ancake_array =[]
for file in files:
    #画像をちぎる処理
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
    # num = img_width / 2483
    num = img_width / 2480
    # 切り上げ
    width = f'{num:.1f}'
    # 係数計算
    num2 = img_height / 3506
    height = f'{num2:.1f}'

    ########################################
    # アンケート番号　を切り取る
    ########################################
    id_width_first = Decimal(width) * 1741
    id_width_first =int(math.floor(float(id_width_first)))

    id_width_end = Decimal(width) * 2191
    id_width_end =int(math.floor(float(id_width_end)))

    id_height_first = Decimal(height) * 430
    id_height_first =int(math.floor(float(id_height_first)))

    id_height_end = Decimal(height) * 610
    id_height_end =int(math.floor(float(id_height_end)))

    id = img[id_height_first:id_height_end,id_width_first:id_width_end] 

    cv2.imwrite('./images/id.jpg', id)
    #アンケートNoの位置をとる　((116, 37), (330, 94))
    ddd = check_IDPre('./images/id.jpg' )
    id_potition = ddd[0]
    idid = ddd[1]

    # if len(idid)== 5:#アンケートNoが5桁なら　かなり下まで　このIfはいく

    #補正値を計算する(330, 94)に補正する (327, 133)
    xx = int(id_potition[2]) - 330
    yy = int(id_potition[3]) - 94#y軸は下にいくほど大きい値となる

    #調査ミサイル
    f = open('aaa.txt', 'a')
    f.write('\n 位置補正Δｘ : ' + str(len(idid)) + ':' + id_potition[2] + '  ' + str( xx ))#右上のx値
    f.write('\n 位置補正Δｙ : ' + id_potition[3] + '  ' + str( yy ))#右上のｙ値
    f.close()
    #調査ミサイルEnd

    #####################################
    # 学年 
    #######################################
    school_year_width_first = Decimal(width) * 200 + xx
    school_year_width_first = int(math.floor(float(school_year_width_first)))

    school_year_width_end = Decimal(width) * 1500 + xx
    school_year_width_end = int(math.floor(float(school_year_width_end)))

    school_year_height_first = Decimal(height) * 450 + yy
    school_year_height_first =int(math.floor(float(school_year_height_first)))

    school_year_height_end = Decimal(height) * 650 + yy
    school_year_height_end = int(math.floor(float(school_year_height_end)))

    school_year = img[school_year_height_first:school_year_height_end,school_year_width_first:school_year_width_end]

    ############################################
    # 英語　質問１ heightがずれるから＋１０
    ############################################
    
    # e_1_width_first = Decimal(width) * 330
    e_1_width_first = Decimal(width) * 335 + xx
    e_1_width_first =int(math.floor(float(e_1_width_first)))

    # e_1_width_end = Decimal(width) * 750
    e_1_width_end = Decimal(width) * 755 + xx
    e_1_width_end =int(math.floor(float(e_1_width_end)))
    
    # e_1_height_first = Decimal(height) * 800
    e_1_height_first = Decimal(height) * 850 + yy
    e_1_height_first =int(math.floor(float(e_1_height_first)))

    # e_1_height_end = Decimal(height) * 950
    e_1_height_end = Decimal(height) * 1000 + yy
    e_1_height_end =int(math.floor(float(e_1_height_end)))


    e_1 = img[e_1_height_first:e_1_height_end,e_1_width_first:e_1_width_end] #英語クラス

    ############################################
    # 英語　質問2 heightがずれるから＋１０
    ############################################
    # e_2_width_first = Decimal(width) * 330
    e_2_width_first = Decimal(width) * 335 + xx
    e_2_width_first =int(math.floor(float(e_2_width_first)))

    # e_2_width_end = Decimal(width) * 750
    e_2_width_end = Decimal(width) * 755 + xx
    e_2_width_end =int(math.floor(float(e_2_width_end)))

    # e_2_height_first = Decimal(height) * 940
    e_2_height_first = Decimal(height) * 990 + yy
    e_2_height_first =int(math.floor(float(e_2_height_first)))

    # e_2_height_end = Decimal(height) * 1090
    e_2_height_end = Decimal(height) * 1140 + yy
    e_2_height_end =int(math.floor(float(e_2_height_end)))

    e_2 = img[e_2_height_first:e_2_height_end,e_2_width_first:e_2_width_end] #英語クラス

    ############################################
    # 英語　質問3 heightがずれるから＋１０
    ############################################
    # e_3_width_first = Decimal(width) * 330
    e_3_width_first = Decimal(width) * 335 + xx
    e_3_width_first =int(math.floor(float(e_3_width_first)))

    # e_3_width_end = Decimal(width) * 750
    e_3_width_end = Decimal(width) * 755 + xx
    e_3_width_end =int(math.floor(float(e_3_width_end)))

    # e_3_height_first = Decimal(height) * 1090
    e_3_height_first = Decimal(height) * 1130 + yy
    e_3_height_first =int(math.floor(float(e_3_height_first)))

    e_3_height_end = Decimal(height) * 1280 + yy
    e_3_height_end =int(math.floor(float(e_3_height_end)))

    e_3 = img[e_3_height_first:e_3_height_end,e_3_width_first:e_3_width_end]

    ############################################
    # 英語　質問4 heightがずれるから＋１０
    ############################################
    # e_4_width_first = Decimal(width) * 330
    e_4_width_first = Decimal(width) * 335 + xx
    e_4_width_first =int(math.floor(float(e_4_width_first)))

    # e_4_width_end = Decimal(width) * 750
    e_4_width_end = Decimal(width) * 755 + xx
    e_4_width_end =int(math.floor(float(e_4_width_end)))

    # e_4_height_first = Decimal(height) * 1240
    e_4_height_first = Decimal(height) * 1270 + yy
    e_4_height_first =int(math.floor(float(e_4_height_first)))

    # e_4_height_end = Decimal(height) * 1390
    e_4_height_end = Decimal(height) * 1420 + yy
    e_4_height_end =int(math.floor(float(e_4_height_end)))

    e_4 = img[e_4_height_first:e_4_height_end,e_4_width_first:e_4_width_end]

    ############################################
    # 英語　質問5 heightがずれるから＋１０
    ############################################
    # e_5_width_first = Decimal(width) * 330
    e_5_width_first = Decimal(width) * 335 + xx
    e_5_width_first =int(math.floor(float(e_5_width_first)))

    # e_5_width_end = Decimal(width) * 750
    e_5_width_end = Decimal(width) * 755 + xx
    e_5_width_end =int(math.floor(float(e_5_width_end)))

    # e_5_height_first = Decimal(height) * 1390
    e_5_height_first = Decimal(height) * 1390 + yy
    e_5_height_first =int(math.floor(float(e_5_height_first)))

    # e_5_height_end = Decimal(height) * 1540
    e_5_height_end = Decimal(height) * 1540 + yy
    e_5_height_end =int(math.floor(float(e_5_height_end)))

    e_5 = img[e_5_height_first:e_5_height_end,e_5_width_first:e_5_width_end]

    ############################################
    # 英語　質問6 heightがずれるから＋１０
    ############################################
    e_6_width_first = Decimal(width) * 335  + xx#430幅
    e_6_width_first =int(math.floor(float(e_6_width_first)))

    e_6_width_end = Decimal(width) * 755 + xx
    e_6_width_end =int(math.floor(float(e_6_width_end)))

    e_6_height_first = Decimal(height) * 1540 + yy
    e_6_height_first =int(math.floor(float(e_6_height_first)))

    e_6_height_end = Decimal(height) * 1690 + yy
    e_6_height_end =int(math.floor(float(e_6_height_end)))

    e_6 = img[e_6_height_first:e_6_height_end,e_6_width_first:e_6_width_end]

    ############################################
    # 英語　質問7 heightがずれるから＋１０
    ############################################
    e_7_width_first = Decimal(width) * 335 + xx
    e_7_width_first =int(math.floor(float(e_7_width_first)))

    e_7_width_end = Decimal(width) * 755 + xx
    e_7_width_end =int(math.floor(float(e_7_width_end)))

    e_7_height_first = Decimal(height) * 1690 + yy
    e_7_height_first =int(math.floor(float(e_7_height_first)))

    e_7_height_end = Decimal(height) * 1840 + yy
    e_7_height_end =int(math.floor(float(e_7_height_end)))

    e_7 = img[e_7_height_first:e_7_height_end,e_7_width_first:e_7_width_end]


    ############################################
    # 理科質問1
    ############################################
    s_1_width_first = Decimal(width) * 1065 + xx
    s_1_width_first =int(math.floor(float(s_1_width_first)))

    s_1_width_end = Decimal(width) * 1485 + xx
    s_1_width_end =int(math.floor(float(s_1_width_end)))

    s_1_height_first = Decimal(height) * 850 + yy
    s_1_height_first =int(math.floor(float(s_1_height_first)))

    s_1_height_end = Decimal(height) * 1000 + yy
    s_1_height_end =int(math.floor(float(s_1_height_end)))

    s_1 = img[s_1_height_first:s_1_height_end,s_1_width_first:s_1_width_end]

    ############################################
    # 理科質問2
    ############################################
    s_2_width_first = Decimal(width) * 1065 + xx
    s_2_width_first =int(math.floor(float(s_2_width_first)))

    s_2_width_end = Decimal(width) * 1485 + xx
    s_2_width_end =int(math.floor(float(s_2_width_end)))

    s_2_height_first = Decimal(height) * 990 + yy
    s_2_height_first =int(math.floor(float(s_2_height_first)))

    s_2_height_end = Decimal(height) * 1140 + yy
    s_2_height_end =int(math.floor(float(s_2_height_end)))

    s_2 = img[s_2_height_first:s_2_height_end,s_2_width_first:s_2_width_end]

    ############################################
    # 理科質問3
    ############################################
    s_3_width_first = Decimal(width) * 1065 + xx
    s_3_width_first =int(math.floor(float(s_3_width_first)))

    s_3_width_end = Decimal(width) * 1485 + xx
    s_3_width_end =int(math.floor(float(s_3_width_end)))

    s_3_height_first = Decimal(height) * 1130 + yy
    s_3_height_first =int(math.floor(float(s_3_height_first)))

    s_3_height_end = Decimal(height) * 1280 + yy
    s_3_height_end =int(math.floor(float(s_3_height_end)))

    s_3 = img[s_3_height_first:s_3_height_end,s_3_width_first:s_3_width_end]

    ############################################
    # 理科質問4
    ############################################
    s_4_width_first = Decimal(width) * 1065 + xx
    s_4_width_first =int(math.floor(float(s_4_width_first)))

    s_4_width_end = Decimal(width) * 1485 + xx
    s_4_width_end =int(math.floor(float(s_4_width_end)))

    s_4_height_first = Decimal(height) * 1250 + yy
    s_4_height_first =int(math.floor(float(s_4_height_first)))

    s_4_height_end = Decimal(height) * 1400 + yy
    s_4_height_end =int(math.floor(float(s_4_height_end)))

    s_4 = img[s_4_height_first:s_4_height_end,s_4_width_first:s_4_width_end]

    ############################################
    # 理科質問5
    ############################################
    s_5_width_first = Decimal(width) * 1065 + xx
    s_5_width_first =int(math.floor(float(s_5_width_first)))

    s_5_width_end = Decimal(width) * 1485 + xx
    s_5_width_end =int(math.floor(float(s_5_width_end)))

    s_5_height_first = Decimal(height) * 1400 + yy
    s_5_height_first =int(math.floor(float(s_5_height_first)))

    s_5_height_end = Decimal(height) * 1550 + yy
    s_5_height_end =int(math.floor(float(s_5_height_end)))

    s_5 = img[s_5_height_first:s_5_height_end,s_5_width_first:s_5_width_end]

    ############################################
    # 理科質問6
    ############################################
    s_6_width_first = Decimal(width) * 1055 + xx
    s_6_width_first =int(math.floor(float(s_6_width_first)))

    s_6_width_end = Decimal(width) * 1485 + xx
    s_6_width_end =int(math.floor(float(s_6_width_end)))

    s_6_height_first = Decimal(height) * 1520 + yy
    s_6_height_first =int(math.floor(float(s_6_height_first)))

    s_6_height_end = Decimal(height) * 1670 + yy
    s_6_height_end =int(math.floor(float(s_6_height_end)))

    s_6 = img[s_6_height_first:s_6_height_end,s_6_width_first:s_6_width_end]

    ############################################
    # 理科質問7
    ############################################
    s_7_width_first = Decimal(width) * 1065 + xx
    s_7_width_first =int(math.floor(float(s_7_width_first)))

    s_7_width_end = Decimal(width) * 1485 + xx
    s_7_width_end =int(math.floor(float(s_7_width_end)))

    s_7_height_first = Decimal(height) * 1670 + yy
    s_7_height_first =int(math.floor(float(s_7_height_first)))

    s_7_height_end = Decimal(height) * 1820 + yy
    s_7_height_end =int(math.floor(float(s_7_height_end)))

    s_7 = img[s_7_height_first:s_7_height_end,s_7_width_first:s_7_width_end]

    ############################################
    # 数学質問1
    ############################################
    m_1_width_first = Decimal(width) * 1780 + xx
    m_1_width_first =int(math.floor(float(m_1_width_first)))

    m_1_width_end = Decimal(width) * 2200 + xx
    m_1_width_end =int(math.floor(float(m_1_width_end)))

    m_1_height_first = Decimal(height) * 840 + yy
    m_1_height_first =int(math.floor(float(m_1_height_first)))

    m_1_height_end = Decimal(height) * 990 + yy
    m_1_height_end =int(math.floor(float(m_1_height_end)))

    m_1 = img[m_1_height_first:m_1_height_end,m_1_width_first:m_1_width_end]

    ############################################
    # 数学質問2
    ############################################
    m_2_width_first = Decimal(width) * 1780 + xx
    m_2_width_first =int(math.floor(float(m_2_width_first)))

    m_2_width_end = Decimal(width) * 2200 + xx
    m_2_width_end =int(math.floor(float(m_2_width_end)))

    m_2_height_first = Decimal(height) * 980 + yy
    m_2_height_first =int(math.floor(float(m_2_height_first)))

    m_2_height_end = Decimal(height) * 1130 + yy
    m_2_height_end =int(math.floor(float(m_2_height_end)))

    m_2 = img[m_2_height_first:m_2_height_end,m_2_width_first:m_2_width_end]

    ############################################
    # 数学質問3
    ############################################
    m_3_width_first = Decimal(width) * 1780 + xx
    m_3_width_first =int(math.floor(float(m_3_width_first)))

    m_3_width_end = Decimal(width) * 2200 + xx
    m_3_width_end =int(math.floor(float(m_3_width_end)))

    m_3_height_first = Decimal(height) * 1110 + yy
    m_3_height_first =int(math.floor(float(m_3_height_first)))

    m_3_height_end = Decimal(height) * 1260 + yy
    m_3_height_end =int(math.floor(float(m_3_height_end)))

    m_3 = img[m_3_height_first:m_3_height_end,m_3_width_first:m_3_width_end]

    ############################################
    # 数学質問4
    ############################################
    m_4_width_first = Decimal(width) * 1780 + xx
    m_4_width_first =int(math.floor(float(m_4_width_first)))

    m_4_width_end = Decimal(width) * 2200 + xx
    m_4_width_end =int(math.floor(float(m_4_width_end)))

    m_4_height_first = Decimal(height) * 1240 + yy
    m_4_height_first =int(math.floor(float(m_4_height_first)))

    m_4_height_end = Decimal(height) * 1390 + yy
    m_4_height_end =int(math.floor(float(m_4_height_end)))

    m_4 = img[m_4_height_first:m_4_height_end,m_4_width_first:m_4_width_end]

    ############################################
    # 数学質問5
    ############################################
    m_5_width_first = Decimal(width) * 1780 + xx
    m_5_width_first =int(math.floor(float(m_5_width_first)))

    m_5_width_end = Decimal(width) * 2200 + xx
    m_5_width_end =int(math.floor(float(m_5_width_end)))

    m_5_height_first = Decimal(height) * 1390 + yy
    m_5_height_first =int(math.floor(float(m_5_height_first)))

    m_5_height_end = Decimal(height) * 1540 + yy
    m_5_height_end =int(math.floor(float(m_5_height_end)))

    m_5 = img[m_5_height_first:m_5_height_end,m_5_width_first:m_5_width_end]

    ############################################
    # 数学質問6
    ############################################
    m_6_width_first = Decimal(width) * 1780 + xx
    m_6_width_first =int(math.floor(float(m_6_width_first)))

    m_6_width_end = Decimal(width) * 2200 + xx
    m_6_width_end =int(math.floor(float(m_6_width_end)))

    m_6_height_first = Decimal(height) * 1520 + yy
    m_6_height_first =int(math.floor(float(m_6_height_first)))

    m_6_height_end = Decimal(height) * 1670 + yy
    m_6_height_end =int(math.floor(float(m_6_height_end)))

    m_6 = img[m_6_height_first:m_6_height_end,m_6_width_first:m_6_width_end]

    ############################################
    # 数学質問7
    ############################################
    m_7_width_first = Decimal(width) * 1780 + xx
    m_7_width_first =int(math.floor(float(m_7_width_first)))

    m_7_width_end = Decimal(width) * 2200 + xx
    m_7_width_end =int(math.floor(float(m_7_width_end)))

    m_7_height_first = Decimal(height) * 1670 + yy
    m_7_height_first =int(math.floor(float(m_7_height_first)))

    m_7_height_end = Decimal(height) * 1820 + yy
    m_7_height_end =int(math.floor(float(m_7_height_end)))

    m_7 = img[m_7_height_first:m_7_height_end,m_7_width_first:m_7_width_end]

    ############################################
    # 国語質問1 +10
    ############################################
    j_1_width_first = Decimal(width) * 340 + xx
    j_1_width_first =int(math.floor(float(j_1_width_first)))

    j_1_width_end = Decimal(width) * 760 + xx
    j_1_width_end =int(math.floor(float(j_1_width_end)))

    j_1_height_first = Decimal(height) * 2040 + yy
    j_1_height_first =int(math.floor(float(j_1_height_first)))

    j_1_height_end = Decimal(height) * 2190 + yy
    j_1_height_end =int(math.floor(float(j_1_height_end)))

    j_1 = img[j_1_height_first:j_1_height_end,j_1_width_first:j_1_width_end]

    ############################################
    # 国語質問2 +10
    ############################################
    j_2_width_first = Decimal(width) * 340 + xx
    j_2_width_first =int(math.floor(float(j_2_width_first)))

    j_2_width_end = Decimal(width) * 760 + xx
    j_2_width_end =int(math.floor(float(j_2_width_end)))

    j_2_height_first = Decimal(height) * 2180 + yy
    j_2_height_first =int(math.floor(float(j_2_height_first)))

    j_2_height_end = Decimal(height) * 2330 + yy
    j_2_height_end =int(math.floor(float(j_2_height_end)))

    j_2 = img[j_2_height_first:j_2_height_end,j_2_width_first:j_2_width_end]

    ############################################
    # 国語質問3 +10
    ############################################
    j_3_width_first = Decimal(width) * 340 + xx
    j_3_width_first =int(math.floor(float(j_3_width_first)))

    j_3_width_end = Decimal(width) * 760 + xx
    j_3_width_end =int(math.floor(float(j_3_width_end)))

    j_3_height_first = Decimal(height) * 2320 + yy
    j_3_height_first =int(math.floor(float(j_3_height_first)))

    j_3_height_end = Decimal(height) * 2470 + yy
    j_3_height_end =int(math.floor(float(j_3_height_end)))

    j_3 = img[j_3_height_first:j_3_height_end,j_3_width_first:j_3_width_end]

    ############################################
    # 国語質問4 +10
    ############################################
    j_4_width_first = Decimal(width) * 340 + xx
    j_4_width_first =int(math.floor(float(j_4_width_first)))

    j_4_width_end = Decimal(width) * 760 + xx
    j_4_width_end =int(math.floor(float(j_4_width_end)))

    j_4_height_first = Decimal(height) * 2470 + yy
    j_4_height_first =int(math.floor(float(j_4_height_first)))

    j_4_height_end = Decimal(height) * 2620 + yy
    j_4_height_end =int(math.floor(float(j_4_height_end)))

    j_4 = img[j_4_height_first:j_4_height_end,j_4_width_first:j_4_width_end]

    ############################################
    # 国語質問5 +10
    ############################################
    j_5_width_first = Decimal(width) * 340 + xx
    j_5_width_first =int(math.floor(float(j_5_width_first)))

    j_5_width_end = Decimal(width) * 760 + xx
    j_5_width_end =int(math.floor(float(j_5_width_end)))

    j_5_height_first = Decimal(height) * 2600 + yy
    j_5_height_first =int(math.floor(float(j_5_height_first)))

    j_5_height_end = Decimal(height) * 2750 + yy
    j_5_height_end =int(math.floor(float(j_5_height_end)))

    j_5 = img[j_5_height_first:j_5_height_end,j_5_width_first:j_5_width_end]

    ############################################
    # 国語質問6 +10
    ############################################
    j_6_width_first = Decimal(width) * 340 + xx
    j_6_width_first =int(math.floor(float(j_6_width_first)))

    j_6_width_end = Decimal(width) * 760 + xx
    j_6_width_end =int(math.floor(float(j_6_width_end)))

    j_6_height_first = Decimal(height) * 2740 + yy
    j_6_height_first =int(math.floor(float(j_6_height_first)))

    j_6_height_end = Decimal(height) * 2890 + yy
    j_6_height_end =int(math.floor(float(j_6_height_end)))

    j_6 = img[j_6_height_first:j_6_height_end,j_6_width_first:j_6_width_end]


    ############################################
    # 国語質問7 +10
    ############################################
    j_7_width_first = Decimal(width) * 340 + xx
    j_7_width_first =int(math.floor(float(j_7_width_first)))

    j_7_width_end = Decimal(width) * 760 + xx
    j_7_width_end =int(math.floor(float(j_7_width_end)))

    j_7_height_first = Decimal(height) * 2860 + yy
    j_7_height_first =int(math.floor(float(j_7_height_first)))

    j_7_height_end = Decimal(height) * 3010 + yy
    j_7_height_end =int(math.floor(float(j_7_height_end)))

    j_7 = img[j_7_height_first:j_7_height_end,j_7_width_first:j_7_width_end]

    ############################################
    # 社会質問1 +10
    ############################################
    so_1_width_first = Decimal(width) * 1080  + xx#
    so_1_width_first =int(math.floor(float(so_1_width_first)))

    so_1_width_end = Decimal(width) * 1500 + xx
    so_1_width_end =int(math.floor(float(so_1_width_end)))

    so_1_height_first = Decimal(height) * 2030 + yy
    so_1_height_first =int(math.floor(float(so_1_height_first)))

    so_1_height_end = Decimal(height) * 2180 + yy
    so_1_height_end =int(math.floor(float(so_1_height_end)))

    so_1 = img[so_1_height_first:so_1_height_end,so_1_width_first:so_1_width_end]


    ############################################
    # 社会質問2 +10
    ############################################
    so_2_width_first = Decimal(width) * 1070 + xx
    so_2_width_first =int(math.floor(float(so_2_width_first)))

    so_2_width_end = Decimal(width) * 1490 + xx
    so_2_width_end =int(math.floor(float(so_2_width_end)))

    so_2_height_first = Decimal(height) * 2175 + yy
    so_2_height_first =int(math.floor(float(so_2_height_first)))

    so_2_height_end = Decimal(height) * 2325 + yy
    so_2_height_end =int(math.floor(float(so_2_height_end)))

    so_2 = img[so_2_height_first:so_2_height_end,so_2_width_first:so_2_width_end]

    ############################################
    # 社会質問3 +10
    ############################################
    so_3_width_first = Decimal(width) * 1070 + xx
    so_3_width_first =int(math.floor(float(so_3_width_first)))

    so_3_width_end = Decimal(width) * 1490 + xx
    so_3_width_end =int(math.floor(float(so_3_width_end)))

    so_3_height_first = Decimal(height) * 2320 + yy
    so_3_height_first =int(math.floor(float(so_3_height_first)))

    so_3_height_end = Decimal(height) * 2470 + yy
    so_3_height_end =int(math.floor(float(so_3_height_end)))

    so_3 = img[so_3_height_first:so_3_height_end,so_3_width_first:so_3_width_end]

    ############################################
    # 社会質問4 +10
    ############################################
    so_4_width_first = Decimal(width) * 1070 + xx
    so_4_width_first =int(math.floor(float(so_4_width_first)))

    so_4_width_end = Decimal(width) * 1490 + xx
    so_4_width_end =int(math.floor(float(so_4_width_end)))

    so_4_height_first = Decimal(height) * 2455 + yy
    so_4_height_first =int(math.floor(float(so_4_height_first)))

    so_4_height_end = Decimal(height) * 2605 + yy
    so_4_height_end =int(math.floor(float(so_4_height_end)))

    so_4 = img[so_4_height_first:so_4_height_end,so_4_width_first:so_4_width_end]

    ############################################
    # 社会質問5 +10
    ############################################
    so_5_width_first = Decimal(width) * 1070 + xx
    so_5_width_first =int(math.floor(float(so_5_width_first)))

    so_5_width_end = Decimal(width) * 1490 + xx
    so_5_width_end =int(math.floor(float(so_5_width_end)))

    so_5_height_first = Decimal(height) * 2600 + yy
    so_5_height_first =int(math.floor(float(so_5_height_first)))

    so_5_height_end = Decimal(height) * 2750 + yy
    so_5_height_end =int(math.floor(float(so_5_height_end)))

    so_5 = img[so_5_height_first:so_5_height_end,so_5_width_first:so_5_width_end]

    ############################################
    # 社会質問6 +10
    ############################################
    so_6_width_first = Decimal(width) * 1070 + xx
    so_6_width_first =int(math.floor(float(so_6_width_first)))

    so_6_width_end = Decimal(width) * 1490 + xx
    so_6_width_end =int(math.floor(float(so_6_width_end)))

    so_6_height_first = Decimal(height) * 2740 + yy
    so_6_height_first =int(math.floor(float(so_6_height_first)))

    so_6_height_end = Decimal(height) * 2890 + yy
    so_6_height_end =int(math.floor(float(so_6_height_end)))

    so_6 = img[so_6_height_first:so_6_height_end,so_6_width_first:so_6_width_end]

    ############################################
    # 社会質問7 +10
    ############################################
    so_7_width_first = Decimal(width) * 1070 + xx
    so_7_width_first =int(math.floor(float(so_7_width_first)))

    so_7_width_end = Decimal(width) * 1490 + xx
    so_7_width_end =int(math.floor(float(so_7_width_end)))

    so_7_height_first = Decimal(height) * 2860 + yy
    so_7_height_first =int(math.floor(float(so_7_height_first)))

    so_7_height_end = Decimal(height) * 3010 + yy
    so_7_height_end =int(math.floor(float(so_7_height_end)))

    so_7 = img[so_7_height_first:so_7_height_end,so_7_width_first:so_7_width_end]

    ############################################
    # その他質問1 +10
    ############################################
    o_1_width_first = Decimal(width) * 1780 + xx
    o_1_width_first =int(math.floor(float(o_1_width_first)))

    o_1_width_end = Decimal(width) * 2200 + xx
    o_1_width_end =int(math.floor(float(o_1_width_end)))

    o_1_height_first = Decimal(height) * 2020 + yy
    o_1_height_first =int(math.floor(float(o_1_height_first)))

    o_1_height_end = Decimal(height) * 2170 + yy
    o_1_height_end =int(math.floor(float(o_1_height_end)))

    o_1 = img[o_1_height_first:o_1_height_end,o_1_width_first:o_1_width_end]

    ############################################
    # その他質問2 +10
    ############################################
    # o_2_width_first = Decimal(width) * 1830
    o_2_width_first = Decimal(width) * 1780 + xx
    o_2_width_first =int(math.floor(float(o_2_width_first)))

    o_2_width_end = Decimal(width) * 2200 + xx
    o_2_width_end =int(math.floor(float(o_2_width_end)))

    o_2_height_first = Decimal(height) * 2170 + yy
    o_2_height_first =int(math.floor(float(o_2_height_first)))

    o_2_height_end = Decimal(height) * 2320 + yy
    o_2_height_end =int(math.floor(float(o_2_height_end)))

    o_2 = img[o_2_height_first:o_2_height_end,o_2_width_first:o_2_width_end]

    ############################################
    # その他質問3 +10
    ############################################
    o_3_width_first = Decimal(width) * 1780 + xx
    o_3_width_first =int(math.floor(float(o_3_width_first)))

    o_3_width_end = Decimal(width) * 2200 + xx
    o_3_width_end =int(math.floor(float(o_3_width_end)))

    o_3_height_first = Decimal(height) * 2310 + yy
    o_3_height_first =int(math.floor(float(o_3_height_first)))

    o_3_height_end = Decimal(height) * 2460 + yy
    o_3_height_end =int(math.floor(float(o_3_height_end)))

    o_3 = img[o_3_height_first:o_3_height_end,o_3_width_first:o_3_width_end]

    ############################################
    # その他質問4 +10
    ############################################
    o_4_width_first = Decimal(width) * 1780 + xx
    o_4_width_first =int(math.floor(float(o_4_width_first)))

    o_4_width_end = Decimal(width) * 2200 + xx
    o_4_width_end =int(math.floor(float(o_4_width_end)))

    o_4_height_first = Decimal(height) * 2450 + yy
    o_4_height_first =int(math.floor(float(o_4_height_first)))

    o_4_height_end = Decimal(height) * 2600 + yy
    o_4_height_end =int(math.floor(float(o_4_height_end)))

    o_4 = img[o_4_height_first:o_4_height_end,o_4_width_first:o_4_width_end]

    ############################################
    # その他質問5 +10
    ############################################
    o_5_width_first = Decimal(width) * 1780 + xx
    o_5_width_first =int(math.floor(float(o_5_width_first)))

    o_5_width_end = Decimal(width) * 2200 + xx
    o_5_width_end =int(math.floor(float(o_5_width_end)))

    o_5_height_first = Decimal(height) * 2570 + yy
    o_5_height_first =int(math.floor(float(o_5_height_first)))

    o_5_height_end = Decimal(height) * 2720 + yy
    o_5_height_end =int(math.floor(float(o_5_height_end)))

    o_5 = img[o_5_height_first:o_5_height_end,o_5_width_first:o_5_width_end]

    ############################################
    # その他質問6 +10
    ############################################
    o_6_width_first = Decimal(width) * 1780 + xx
    o_6_width_first =int(math.floor(float(o_6_width_first)))

    o_6_width_end = Decimal(width) * 2200 + xx
    o_6_width_end =int(math.floor(float(o_6_width_end)))

    o_6_height_first = Decimal(height) * 2720 + yy
    o_6_height_first =int(math.floor(float(o_6_height_first)))

    o_6_height_end = Decimal(height) * 2870 + yy
    o_6_height_end =int(math.floor(float(o_6_height_end)))

    o_6 = img[o_6_height_first:o_6_height_end,o_6_width_first:o_6_width_end]

    ############################################
    # その他　質問７　
    ###########################################
    o_7_width_first = Decimal(width) * 1780 + xx
    o_7_width_first =int(math.floor(float(o_7_width_first)))

    o_7_width_end = Decimal(width) * 2200 + xx
    o_7_width_end =int(math.floor(float(o_7_width_end)))

    o_7_height_first = Decimal(height) * 2850 + yy
    o_7_height_first =int(math.floor(float(o_7_height_first)))

    o_7_height_end = Decimal(height) * 3000 + yy
    o_7_height_end =int(math.floor(float(o_7_height_end)))

    o_7 = img[o_7_height_first:o_7_height_end,o_7_width_first:o_7_width_end]

    ##############################################
    # 英語クラス
    ###############################################
    e_class_width_first = Decimal(width) * 310 + xx
    e_class_width_first =int(math.floor(float(e_class_width_first)))

    e_class_width_end = Decimal(width) * 860 + xx
    e_class_width_end =int(math.floor(float(e_class_width_end)))

    e_class_height_first = Decimal(height) * 650 + yy
    e_class_height_first =int(math.floor(float(e_class_height_first)))

    e_class_height_end = Decimal(height) * 850 + yy
    e_class_height_end =int(math.floor(float(e_class_height_end)))

    e_class = img[e_class_height_first:e_class_height_end,e_class_width_first:e_class_width_end] #英語クラス

    ############################################
    # 理科クラス　
    ############################################
    s_class_width_first = Decimal(width) * 1030 + xx
    s_class_width_first =int(math.floor(float(s_class_width_first)))

    s_class_width_end = Decimal(width) * 1580 + xx
    s_class_width_end =int(math.floor(float(s_class_width_end)))

    s_class_height_first = Decimal(height) * 650 + yy
    s_class_height_first =int(math.floor(float(s_class_height_first)))

    s_class_height_end = Decimal(height) * 850 + yy
    s_class_height_end =int(math.floor(float(s_class_height_end)))

    s_class = img[s_class_height_first:s_class_height_end,s_class_width_first:s_class_width_end]

    ############################################
    # 数学クラス　
    ############################################
    m_class_width_first = Decimal(width) * 1750 + xx
    m_class_width_first =int(math.floor(float(m_class_width_first)))

    m_class_width_end = Decimal(width) * 2300 + xx
    m_class_width_end =int(math.floor(float(m_class_width_end)))

    m_class_height_first = Decimal(height) * 650 + yy
    m_class_height_first =int(math.floor(float(m_class_height_first)))

    m_class_height_end = Decimal(height) * 850 + yy
    m_class_height_end =int(math.floor(float(m_class_height_end)))

    m_class = img[m_class_height_first:m_class_height_end,m_class_width_first:m_class_width_end]

    ############################################
    # 国語クラス　
    ############################################
    j_class_width_first = Decimal(width) * 305 + xx
    j_class_width_first =int(math.floor(float(j_class_width_first)))

    j_class_width_end = Decimal(width) * 855 + xx
    j_class_width_end =int(math.floor(float(j_class_width_end)))

    j_class_height_first = Decimal(height) * 1840 + yy
    j_class_height_first =int(math.floor(float(j_class_height_first)))

    j_class_height_end = Decimal(height) * 2040 + yy
    j_class_height_end =int(math.floor(float(j_class_height_end)))

    j_class = img[j_class_height_first:j_class_height_end,j_class_width_first:j_class_width_end]

    ############################################
    # 社会クラス　
    ############################################
    so_class_width_first = Decimal(width) * 1050 + xx
    so_class_width_first =int(math.floor(float(so_class_width_first)))

    so_class_width_end = Decimal(width) * 1600 + xx
    so_class_width_end =int(math.floor(float(so_class_width_end)))

    so_class_height_first = Decimal(height) * 1840 + yy
    so_class_height_first =int(math.floor(float(so_class_height_first)))

    so_class_height_end = Decimal(height) * 2040 + yy
    so_class_height_end =int(math.floor(float(so_class_height_end)))

    so_class = img[so_class_height_first:so_class_height_end,so_class_width_first:so_class_width_end]

    ##############################################
    # その他　クラス　幅
    ##############################################
    o_class_width_first = Decimal(width) * 1750 + xx
    o_class_width_first =int(math.floor(float(o_class_width_first)))

    o_class_width_end = Decimal(width) * 2300 + xx
    o_class_width_end =int(math.floor(float(o_class_width_end)))

    o_class_height_first = Decimal(height) * 1840 + yy
    o_class_height_first =int(math.floor(float(o_class_height_first)))

    o_class_height_end = Decimal(height) * 2040 + yy
    o_class_height_end =int(math.floor(float(o_class_height_end)))

    o_class = img[o_class_height_first:o_class_height_end,o_class_width_first:o_class_width_end]

    ########################################
    # アンケート番号
    ########################################
    id_width_first = Decimal(width) * 1741 + xx
    id_width_first =int(math.floor(float(id_width_first)))

    id_width_end = Decimal(width) * 2191 + xx
    id_width_end =int(math.floor(float(id_width_end)))

    id_height_first = Decimal(height) * 430 + yy
    id_height_first =int(math.floor(float(id_height_first)))

    id_height_end = Decimal(height) * 610 + yy
    id_height_end =int(math.floor(float(id_height_end)))

    id = img[id_height_first:id_height_end,id_width_first:id_width_end] 

    cv2.imwrite('./images/id.jpg', id)

    # 学年
    cv2.imwrite('./images/school_year.jpg', school_year)

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

    # 画像をちぎる処理 アンケート画像判定処理　imagesフォルダにある画像名を全部、配列にいれる。

    from chainer.cuda import to_cpu
    retval = []
    id = ''
    school = ''

    import glob
    test_image_url_array =[]
    files = glob.glob("./images/*.jpg")#正規表現　[a-z_]-\d数字指定か文字列指定

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

    # モデル強化のために画像を保存する
    # 学年
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_school_year.jpg', school_year)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_class_1.jpg', e_class)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_1_1.jpg', e_1)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_2_1.jpg', e_2)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_3_1.jpg', e_3)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_4_1.jpg', e_4)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_5_1.jpg', e_5)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_6_1.jpg', e_6)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_7_1.jpg', e_7)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_class_2.jpg', s_class)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_1_2.jpg', s_1)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_2_2.jpg', s_2)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_3_2.jpg', s_3)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_4_2.jpg', s_4)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_5_2.jpg', s_5)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_6_2.jpg', s_6)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_7_2.jpg', s_7)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_class_3.jpg', m_class)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_1_3.jpg', m_1)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_2_3.jpg', m_2)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_3_3.jpg', m_3)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_4_3.jpg', m_4)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_5_3.jpg', m_5)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_6_3.jpg', m_6)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_7_3.jpg', m_7)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_class_4.jpg', j_class)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_1_4.jpg', j_1)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_2_4.jpg', j_2)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_3_4.jpg', j_3)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_4_4.jpg', j_4)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_5_4.jpg', j_5)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_6_4.jpg', j_6)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_7_4.jpg', j_7)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_class_5.jpg', so_class)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_1_5.jpg', so_1)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_2_5.jpg', so_2)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_3_5.jpg', so_3)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_4_5.jpg', so_4)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_5_5.jpg', so_5)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_6_5.jpg', so_6)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_7_5.jpg', so_7)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_class_6.jpg', o_class)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_1_6.jpg', o_1)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_2_6.jpg', o_2)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_3_6.jpg', o_3)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_4_6.jpg', o_4)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_5_6.jpg', o_5)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_6_6.jpg', o_6)
    cv2.imwrite('/var/www/html/shinzemi/storage/app/public/kobetsu/' + str(teacher_label) + '_question_7_6.jpg', o_7)


    teacher_label = check_ID( school )
    school_name = school.replace('./images/','').replace('.jpg','')#ファイル名部分のみにする
    school_data= convert_test_dataGaku(school, (INPUT_WIDTH_GAKU, INPUT_HEIGHT_GAKU))
    with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
        teacher_labels = modelGaku.predictor(school_data)

        teacher_labels = to_cpu(teacher_labels.array)
        teacher_label = teacher_labels.argmax(axis=1)[0]
        teacher_label = teacher_label.astype(np.object)
        
        
        #精度がわるいものを９９と表示する
        irregular_labels = str(teacher_labels)
        irregular_labels = irregular_labels.replace('[','')
        irregular_labels = irregular_labels.replace(']','')
        irregular_labels = irregular_labels.split()
        answers = []
        for data in irregular_labels:
            answers.append(float(data))
        answers = sorted(answers, reverse=True)
        sabun = answers[0] - answers[1]
        if sabun < 150 or answers[0] < 0:#後で修正　1200を１００に
            teacher_label = '89'#'読み取れてません'
        
        
        #if teacher_label > 0:
        teacher_label = int(teacher_label) + 10

        retval.append( {school_name:teacher_label} )  # 学年の値
    
    i = 0
    class0 = 0

    for test_image_url in test_image_url_array:
        image_name = test_image_url.replace('./images/','').replace('.jpg','')#ファイル名部分のみにする
        # image_name = test_image_url.replace('/var/www/html/shinzemi/ancake/images/','').replace('.jpg','')#ファイル名部分のみにする
        if 'class' in image_name:#ABCDE判別 ファイル名にclassってはいってるかどうか
            test_data= convert_test_dataABC(test_image_url, (INPUT_WIDTH_ABC, INPUT_HEIGHT_ABC))
            with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
              teacher_labels = modelABC.predictor(test_data)
              teacher_labels = to_cpu(teacher_labels.array)
              teacher_label = teacher_labels.argmax(axis=1)[0]
              #teacher_label2 = teacher_labels.argmax(axis=1)[1]
              #精度がわるいものを９９と表示する
              irregular_labels = str(teacher_labels)
              irregular_labels = irregular_labels.replace('[','')
              irregular_labels = irregular_labels.replace(']','')
              irregular_labels = irregular_labels.split()
              answers = []
              for data in irregular_labels:
                  answers.append(float(data))
              answers = sorted(answers, reverse=True)
              sabun = answers[0] - answers[1]
              if sabun < 150 or answers[0] < 0:#後で修正　1200を１００に
                  irregular = '99'#'読み取れてません'
                  retval.append( {image_name:irregular} ) #
                  
                  #調査ミサイル
                  f = open('aaa.txt', 'a')
                  f.write('\n クラス：' + id_name + ":" +image_name + str(answers[0]) + " " + str(answers[1]) + " " + str(answers[2]) + " " + str(answers[3]) + " " + str(answers[4]) + " " + str(answers[5]) )#teacher_label
                  f.write('\n 差分：' + str(sabun))#teacher_label2
                  f.close()
                  #調査ミサイルEnd
              else:
                  if teacher_label == 0:
                    teacher_label = teacher_label.astype(np.object)
                    retval.append( {image_name:teacher_label} ) # レ点なし
                    # retval.append( {image_name:'0'} ) # レ点なし
                    class0 = 1
                  elif teacher_label == 1:
                    teacher_label = teacher_label.astype(np.object)
                    # retval.append( {image_name:'A'} ) # 1にレ点あり
                    retval.append( {image_name:teacher_label} ) # 1にレ点あり
                    class0 = 0
                  elif teacher_label == 2:
                    teacher_label = teacher_label.astype(np.object)
                    retval.append( {image_name:teacher_label} ) # 2にレ点あり
                    class0 = 0
                    # retval.append( {image_name:'B'} ) # 2にレ点あり
                  elif teacher_label == 3:
                    teacher_label = teacher_label.astype(np.object)
                    retval.append( {image_name:teacher_label} ) # 3にレ点あり
                    class0 = 0
                    # retval.append( {image_name:'C'} ) # 3にレ点あり
                  elif teacher_label == 4:
                    teacher_label = teacher_label.astype(np.object)
                    retval.append( {image_name:teacher_label} ) # 4にレ点あり
                    class0 = 0
                    # retval.append( {image_name:'D'} ) # 4にレ点あり
                  elif teacher_label == 5:
                    teacher_label = teacher_label.astype(np.object)
                    retval.append( {image_name:teacher_label} ) # 5にレ点あり
                    class0 = 0
                    # retval.append( {image_name:'E'} ) # 5にレ点あり
                  else :
                    teacher_label = teacher_label.astype(np.object)
                    retval.append( {image_name:''}) # 
                    class0 = 0
        elif 'question' in image_name:#ABCDE判別 ファイル名にclassってはいってるかどうか

            if class0 == 1:#クラスがレ点なかったら質問は無効とする。
                teacher_label = 0 # レ点なし
            else :
                test_data= convert_test_data(test_image_url, (INPUT_WIDTH, INPUT_HEIGHT))
                with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
                  teacher_labels = model.predictor(test_data)
                  teacher_labels = to_cpu(teacher_labels.array)
                  teacher_label = teacher_labels.argmax(axis=1)[0]
                  teacher_label = teacher_label.astype(np.object)

                #精度がわるいものを９９と表示する
                irregular_labels = str(teacher_labels)
                irregular_labels = irregular_labels.replace('[','')
                irregular_labels = irregular_labels.replace(']','')
                irregular_labels = irregular_labels.split()
                answers = []
                for data in irregular_labels:
                    answers.append(float(data))
                answers = sorted(answers, reverse=True)
                sabun = answers[0] - answers[1]
                if sabun < 150 or answers[0] < 0:#後で修正　1200を１００に
                    teacher_label = '99'#'読み取れてません'
                    #調査ミサイル
                    f = open('aaa.txt', 'a')
                    f.write('\n 質問：' + id_name + image_name + ":" + str(answers[0]) + " " + str(answers[1]) + " " + str(answers[2]) + " " + str(answers[3]) + " " + str(answers[4])  )#teacher_label
                    f.write('\n 差分：' + str(sabun))#teacher_label2
                    f.close()
                    #調査ミサイルEnd

            retval.append({image_name:teacher_label}) # 
              
    i = i + 1
    # else:
    retval.append( {image_name:teacher_label} ) # 
        
    ancake_array.append(retval)

#複数枚アンケート処理  End 
ancake_array_json = json.dumps(ancake_array)
print(ancake_array_json)


#複数枚アンケート処理  End 







