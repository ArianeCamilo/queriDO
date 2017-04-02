# From a given set of files and a dictionary of regular expressions, create
# derived documents with markdowns as given by the regexes.

from bs4 import BeautifulSoup
import re
import sys, getopt
import glob
import pickle
import os
import ast
import csv

def main(argv):

    # Default parameters
    input_location = ''
    regex_file = ''
    output_path = ''
    tag = 'div'

    try:
        opts, args = getopt.getopt(argv, "hi:r:o:t:", ["help", "input=", "regex=", "output=", "tag="])
    except getopt.GetoptError:
        usage()
        sys.exit(2)
    for opt, arg in opts:
        if opt in ("-h", "--help"):
            usage()
            sys.exit()
        elif opt in ("-i", "--input"):
            if arg[-1] is not '/':
                input_location = arg + '/'
            else:
                input_location = arg
        elif opt in ("-r", "--regex"):
            regex_file = arg
        elif opt in ("-t", "--tag"):
            tag = arg
        elif opt in ("-o", "--output"):
            if arg[-1] is not '/':
                output_path = arg + '/'
            else:
                output_path = arg
    if not opts:
        usage()
        sys.exit(2)

    if output_path == '' or regex_file == '' or input_location == '':
        print("input, regex and output are mandatory parameters.")
        usage()
        sys.exit(2)

    return input_location, regex_file, tag, output_path


def usage():
    print("usage: collect_htmls [--help] [--input=<path>] [--regex=<path and file>] [--tag=<tag to use>] [--output=<path>]")
    print("--help: prints this usage guide and exits.")
    print("--input=<path>: pass in the path of the input htmls.")
    print("--regex=<path and file>: pass in the path and file name of the regex dictionary.")
    print("--tag=<tag to use>: the tag that will be used to wrap the text. Defaults to div.")
    print("--output=<path>: pass in the path where outputs are to be stored.")


def replacement(m):
    return m.group(0)

def mark_down(html, compiled_regexes, tag):
    # - html is the HTML documents to be marked
    # - regexes is a 2 dimensional array of labels and regular expressions.
    # - tag is the hatml tag that will wrap the text detected.
    soup = BeautifulSoup(html, 'html5lib')
    for text in soup.findAll(text=True):
        for i in range(len(compiled_regexes)):
            open_tag = "<" + tag + " class=" + compiled_regexes[i][0] + ">"
            close_tag = "</" + tag + ">"
            match = compiled_regexes[i][1].search(repr(text))
            if match:
                substitution = re.sub(compiled_regexes[i][1],r'{0}{1}{2}'.format(open_tag, match.group(0), close_tag),text)
                text.replaceWith(BeautifulSoup(substitution ,'html.parser'))
    return soup.prettify()

if __name__ == "__main__":
    input_location, regex_file, tag, output_path = main(sys.argv[1:])
    regexes = []
    with open(regex_file, 'r', newline='') as csvfile:
        for row in csv.reader(csvfile, quotechar='"', delimiter=',', quoting=csv.QUOTE_ALL, skipinitialspace=True):
            regexes.append(row)

    compiled_regexes = []
    # regexes should now contain a 2-dimensional array with classes and regexes
    for i in range(len(regexes)):
        # pre-compile the regular expressions for efficiency
        compiled_regexes.append([regexes[i][0], re.compile(regexes[i][1], re.IGNORECASE)])

    for htmlfilename in glob.glob(input_location + '*.html'):
        with open(htmlfilename, 'r', encoding='utf-8') as htmlfile:
            html = htmlfile.read()
            marked_html = mark_down(html, compiled_regexes, tag)
            filename = os.path.split(htmlfilename)[1]
            with open(output_path + filename, 'wb') as f:
                for line in marked_html:
                    f.write(line.encode('utf-8', errors='replace'))
