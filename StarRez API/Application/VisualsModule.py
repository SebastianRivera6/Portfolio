from kivy.app import App
from kivy.uix.label import Label
from kivy.uix.button import Button
from kivy.uix.image import Image
from kivy.uix.floatlayout import FloatLayout
from kivy.clock import Clock
from LockoutBillingModule import *
from GenericDataModule import *



class LockoutsApp(App):
    def build(self):
        self.layout = FloatLayout()

        # Load the background image
        self.background = Image(source="media/background-image.jpg", allow_stretch=True, keep_ratio=False)
        self.layout.add_widget(self.background)

        # Create a label for the text with white color, bold font
        label = Label(text="CSUF Lockouts", font_size=24, color=(1, 1, 1, 1), size_hint=(None, None), size=(300, 50), pos_hint={'center_x': 0.5, 'y': 0.8},  bold=True)
        self.layout.add_widget(label)

        # Add logo image in top left corner
        self.logo = Image(source="media/institution_logo.png", size_hint=(None, None), size=(125, 125), pos_hint={'x': 0, 'y': 0.85})
        self.layout.add_widget(self.logo)

        # Add buttons
        button1 = Button(text="Log Lockouts", size_hint=(None, None), size=(200, 50), pos_hint={'center_x': 0.5, 'y': 0.5})
        button1.bind(on_press=self.on_button1_press)
        self.layout.add_widget(button1)

        button2 = Button(text="Charge Lockouts", size_hint=(None, None), size=(200, 50),pos_hint={'center_x': 0.5, 'y': 0.4})
        button2.bind(on_press=self.on_button2_press)
        self.layout.add_widget(button2)

        #button3 = Button(text="Settings", size_hint=(None, None), size=(200, 50),pos_hint={'center_x': 0.5, 'y': 0.3})
        #button3.bind(on_press=self.on_button3_press)
        #self.layout.add_widget(button3)

        self.loading_label = None  # Initialize loading label

        return self.layout


    def on_button1_press(self, instance):
        self.loading_label = Label(text="Loading...", color=(1, 1, 1, 1), size_hint=(None, None), size=(200, 50),pos_hint={'center_x': 0.5, 'y': 0.3})
        self.layout.add_widget(self.loading_label)
        self.loading_label.opacity = 1  # Show loading message
        Clock.schedule_once(self.load_data_and_display_image, 1)  # Simulate loading time

    def load_data_and_display_image(self, dt):
        # This is where you would execute the GenericDataDriver function
        GenericDataDriver()
        # Simulating loading time
        Clock.schedule_once(self.display_image, 2)

    def display_image(self, dt):
        self.loading_label.opacity = 0  # Hide loading message
        # Show image and message
        self.image = Image(source="media/WizardTuffy.png", size_hint=(None, None), size=(200, 200),pos_hint={'center_x': 0.5, 'y': 0})
        self.layout.add_widget(self.image)
        self.message_label = Label(text="All new lockouts have been logged",font_size=18, color=(0, 0, 0, 1), size_hint=(None, None),size=(300, 50), pos_hint={'center_x': 0.5, 'y': 0}, bold=True)
        self.layout.add_widget(self.message_label)

    def on_button2_press(self, instance):
        self.loading_label = Label(text="Loading...", color=(1, 1, 1, 1), size_hint=(None, None), size=(200, 50),pos_hint={'center_x': 0.5, 'y': 0.3})
        self.layout.add_widget(self.loading_label)
        self.loading_label.opacity = 1  # Show loading message
        Clock.schedule_once(self.load_data_and_display_image2, 1)  # Simulate loading time

    #def on_button3_press(self, instance):


    def load_data_and_display_image2(self, dt):
        # This is where you would execute the GenericDataDriver function
        ChargeDriver()
        # Simulating loading time
        Clock.schedule_once(self.display_image2, 2)

    def display_image2(self, dt):
        self.loading_label.opacity = 0  # Hide loading message
        # Show image and message
        self.image = Image(source="media/WizardTuffy.png", size_hint=(None, None), size=(200, 200),pos_hint={'center_x': 0.5, 'y': 0})
        self.layout.add_widget(self.image)
        self.message_label = Label(text="All new lockouts have been logged",font_size=18, color=(0, 0, 0, 1), size_hint=(None, None),size=(300, 50), pos_hint={'center_x': 0.5, 'y': 0}, bold=True)
        self.layout.add_widget(self.message_label)

