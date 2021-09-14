import glob, json

directory_path = r'./features/**/*.feature'
text = ""

for filepath in glob.iglob(directory_path, recursive=True):
        open_file = open(filepath, 'r')
        text = text + open_file.readlines()[0]


text_distinct = list(set(text.split()))

output_file = open('output.json', 'w')
output_file.write(json.dumps(text_distinct))
output_file.close()
