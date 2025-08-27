



#import cupy
import chainer
from PIL import Image
import numpy as np
#import matplotlib.pyplot as plt

 
INPUT_WIDTH = 128 #32
INPUT_HEIGHT = 36 #32

def data_reshape(width_height_channel_image):
  image_array = np.array(width_height_channel_image)
  return image_array.transpose(2, 0, 1)

import cv2
#import matplotlib.pyplot as plt


import chainer
import chainer.functions as F
import chainer.links as L



from chainer import training,serializers,Chain,datasets,sequential,optimizers,iterators
#from chainer.training import extensions,Trainer
#GPU_ID = 0
#BATCH_SIZE = 16#初期64    レ点２
#MAX_EPOCH = 14# 初期１０　レ点８


####ABCの学習モデルLoad
##CNNを設定する　ニューラルネットワーク　ABC判定用
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
      self.layer2 = L.Linear(1000, 6)
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
####ABC判定 End


####学年の学習モデルLoad

from chainer import serializers
serializers.load_hdf5("./chainer-dogscatsABC-model.h5", modelABC)
#serializers.load_hdf5("./ancake/chainer-dogscatsABC-model.h5", modelABC)

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
      self.layer2 = L.Linear(1000, 5)
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

INPUT_WIDTH_GAKU = 1160 #32   128
INPUT_HEIGHT_GAKU = 160 #32    36
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
      self.layer2 = L.Linear(1000, 13)#　最後は１３個のどれかに振り分け
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
  image = Image.open(image_file_path)
  result_image = image.resize((INPUT_WIDTH,INPUT_HEIGHT),Image.LANCZOS)

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
    os.environ["GOOGLE_APPLICATION_CREDENTIALS"] = "/var/www/ancake/kimura01-a1eecf5c15f4.json"
    import platform

    #対象画像の読み込み
    import sys
    args = sys.argv
    #image_file ="./images/" + args[1]
    image_file = path

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
        
    return(mojiretu)
  
  
  
################アンケートID判定 END###########  
  
  
  
  
  
  
########フォルダにある画像名を全部、配列にいれる。
retval = []

import glob
test_image_url_array =[]
files = glob.glob("./images/*.jpg")#正規表現　[a-z_]-\d数字指定か文字列指定
# files = glob.glob("./images/*.jpg")#正規表現　[a-z_]-\d数字指定か文字列指定
for file in files:
    test_image_url_array += [file]
    #print(file)
    if 'id' in file:#アンケードIDを識別
        test_teacher_label = check_ID( test_image_url )
        retval.append([ image_name,test_teacher_label ])  # アンケートIDの値

from chainer.cuda import to_cpu

i = 0
for test_image_url in test_image_url_array:
    image_name = test_image_url.replace('./images/','').replace('.jpg','')#ファイル名部分のみにする
    # image_name = test_image_url.replace('./images/','').replace('.jpg','')#ファイル名部分のみにする

    if 'id' in image_name:#アンケードIDを識別
       test_teacher_label = check_ID( test_image_url )
#       retval.append([ image_name,test_teacher_label ])  # アンケートIDの値
        
    elif 'school' in image_name:#学年判定 ファイル名にschoolってはいってるかどうか
        print(image_name)
        test_data= convert_test_dataGaku(test_image_url, (INPUT_WIDTH_GAKU, INPUT_HEIGHT_GAKU))
        with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
            test_teacher_labels = modelGaku.predictor(test_data)
            test_teacher_labels = to_cpu(test_teacher_labels.array)
            test_teacher_label = test_teacher_labels.argmax(axis=1)[0]

            retval.append([ image_name,test_teacher_label ])  # レベルが値だっぺ


    elif 'class' in image_name:#ABCDE判別 ファイル名にclassってはいってるかどうか
        test_data= convert_test_dataABC(test_image_url, (INPUT_WIDTH, INPUT_HEIGHT))
        with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
          test_teacher_labels = modelABC.predictor(test_data)
          test_teacher_labels = to_cpu(test_teacher_labels.array)
        #  print(test_teacher_labels)
          test_teacher_label = test_teacher_labels.argmax(axis=1)[0]
          
          if test_teacher_label == 0:
            retval.append([ image_name,0 ]) # レ点なし
          elif test_teacher_label == 1:
            retval.append([ image_name,'A' ]) # 1にレ点あり
          elif test_teacher_label == 2:
            retval.append([ image_name,'B' ]) # 2にレ点あり
          elif test_teacher_label == 3:
            retval.append([ image_name,'C' ]) # 3にレ点あり
          elif test_teacher_label == 4:
            retval.append([ image_name,'D' ]) # 4にレ点あり
          elif test_teacher_label == 5:
            retval.append([ image_name,'E' ]) # 5にレ点あり
          else :
            retval.append([ image_name,'' ]) #  #''
    else:
        test_data= convert_test_data(test_image_url, (INPUT_WIDTH, INPUT_HEIGHT))
        with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
          test_teacher_labels = model.predictor(test_data)
          test_teacher_labels = to_cpu(test_teacher_labels.array)
        #  print(test_teacher_labels)
          test_teacher_label = test_teacher_labels.argmax(axis=1)[0]
          
          retval.append([ image_name,test_teacher_label ]) # レ点なし
    
    i = i + 1


print(retval)









