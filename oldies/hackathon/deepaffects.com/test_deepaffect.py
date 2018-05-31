#from __future__ import print_statement
import deepaffects
from deepaffects.rest import ApiException
from pprint import pprint
from pydub import AudioSegment

# Configure API key authorization: UserSecurity
deepaffects.configuration.api_key['apikey'] = 'KKZTnf80SXuhXXyZWFIF3XOUfIrVF3sl'

# create an instance of the API class

sound = AudioSegment.from_mp3('test.wav')
sound.export('test.mp3', format="mp3")


api_instance = deepaffects.EmotionApi()
body = deepaffects.Audio.from_file(file_name="test.mp3") # Audio | Audio object that needs to be featurized.

try:
    # Find emotion in an audio file
    api_response = api_instance.sync_recognise_emotion(body)
    pprint(api_response)
except ApiException as e:
#    print("Exception when calling EmotionApi->sync_recognise_emotion: %s\n" % e)
    print("Exception when calling EmotionApi->sync_recognise_emotion: %.3f" % e)

# async request
#webhook = 'https://your/webhook/' # str | The webhook url where result from async resource is posted
#request_id = 'request_id_example' # str | Unique identifier for the request (optional)

#try:
    # Find emotion in an audio file
#    api_response = api_instance.async_recognise_emotion(body, webhook, request_id=request_id)
#    pprint(api_response)
#except ApiException as e:
#    print("Exception when calling EmotionApi->async_recognise_emotion: %s\n" % e)
